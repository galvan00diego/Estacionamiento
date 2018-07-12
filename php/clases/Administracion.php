<?php
require_once ("./vendor/autoload.php");
require_once 'AccesoDatos.php';
require_once ("Cochera.php");

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Administracion
{
    private $_id;
    private $_patente;
    private $_marca;
    private $_color;
    private $_foto;
    private $_id_empleado_entrada;
    private $_fecha_ingreso;
    private $_id_cochera;
    private $_id_empleado_salida;
    private $_fecha_salida;
    private $_importe;
    private $_tiempo;

    public function getId(){return $this->_id;}
    public function getPatente(){return $this->_patente;}
    public function getMarca(){return $this->_marca;}
    public function getColor(){return $this->_color;}
    public function getFoto(){return $this->_foto;}
    public function getIdEmpleadoEntrada(){return $this->_id_empleado_entrada;}
    public function getFechaIngreso(){return $this->_fecha_ingreso;}
    public function getIdCochera(){return $this->_id_cochera;}
    public function getIdEmpleadoSalida(){return $this->_id_empleado_salida;}
    public function getFechaSalida(){return $this->_fecha_salida;}
    public function getImporte(){return $this->_importe;}
    public function getTiempo(){return $this->_tiempo;}

    public function __construct($id=NULL,$patente,$marca,$color,$foto,$id_empleado_entrada,$fecha_ingreso,$id_cochera,$id_empleado_salida,$fecha_salida,$importe,$tiempo)
    {
        $this->_id=$id;
        $this->_patente=$patente;
        $this->_marca=$marca;
        $this->_color=$color;
        $this->_foto=$foto;
        $this->_id_empleado_entrada=$id_empleado_entrada;
        $this->_fecha_ingreso=$fecha_ingreso;
        $this->_id_cochera=$id_cochera;
        $this->_id_empleado_salida=$id_empleado_salida;
        $this->_fecha_salida=$fecha_salida;
        $this->_importe=$importe;
        $this->_tiempo=$tiempo;
    }

    public static function ToJson($obj)
    {
        return '{"patente":"'.$obj->getPatente().'", "marca":"'.$obj->getMarca().'" ,"color": "'.$obj->getColor().'", "foto":"'.$obj->getFoto().
            '", "id_empleado_entrada":"'.$obj->getIdEmpleadoEntrada().'", "fecha_ingreso":"'.$obj->getFechaIngreso().'", "id_cochera":"'.$obj->getIdCochera()
            .'", "id_empleado_salida":"'.$obj->getIdEmpleadoSalida().'"}';
    }

    public function Listar()
    {
        $conexion=AccesoDatos::DameUnObjetoAcceso();
        $resultados = $conexion->RetornarConsulta("SELECT * FROM `administracion`");
        $resultados->execute();

        $listado="[";

            while($fila = $resultados->fetch(PDO::FETCH_ASSOC)) 
            {
                $admin=new Administracion(NULL,$fila["patente"],$fila["marca"],$fila["color"],$fila["foto"],$fila["id_empleado_entrada"],$fila["fecha_ingreso"],$fila["id_cochera"],$fila["id_empleado_salida"],NULL,NULL,NULL);
               
                $listado.=Administracion::ToJson($admin).",";
               
            }
            $listado=substr($listado,0,-1);
            $listado.="]";
            return $listado;
    }

    public static function Tiempo()
    {
        
        $tiempo=new DateTime();
        return $tiempo->format(DateTime::ATOM);
    }

    public function Ingreso($request,$response,$foto)
    {
        $parametros = $request->getParsedBody();
        $espacio_libre=Cochera::TraerLibre();
        
        if($espacio_libre)
        {
            if($foto["foto"]["size"]>0)
            {
                $fotoFinal = $parametros["patente"].date("Gis").".".pathinfo($foto["foto"]["name"] , PATHINFO_EXTENSION);
                $rutaFoto = __DIR__."./img/".$fotoFinal;

                $ingreso=new Administracion(NULL,$parametros["patente"],$parametros["marca"],$parametros["color"],$fotoFinal,
                $parametros["id_empleado_entrada"],Administracion::Tiempo(),$espacio_libre,NULL,
                NULL,NULL,NULL);
            
                if(move_uploaded_file($foto["foto"]["tmp_name"] , $rutaFoto))
                    $response->getBody()->write("La foto se cargo correctamente.");
                else
                    $response->getBody()->write("Error al cargar la foto.");
            }
            else
            {
                $ingreso=new Administracion(NULL,$parametros["patente"],$parametros["marca"],$parametros["color"],NULL,
                $parametros["id_empleado_entrada"],Administracion::Tiempo(),$espacio_libre,NULL,
                NULL,NULL,NULL);
            }
            $conexion=AccesoDatos::DameUnObjetoAcceso();
            $resultados = $conexion->RetornarConsulta("INSERT INTO `administracion`(`patente`, `marca`, `color`, `foto`, `id_empleado_entrada`, `fecha_ingreso`, `id_cochera`)
            VALUES ('".$ingreso->getPatente()."' , '".$ingreso->getMarca()."' ,'".$ingreso->getColor()."','".$ingreso->getFoto()."' ,'".$ingreso->getIdEmpleadoEntrada()."' ,'".$ingreso->getFechaIngreso()
            ." ','".$ingreso->getIdCochera()."')");

            if($resultados->execute()&&Cochera::Aparcar($espacio_libre))
            {    
                $response->getBody()->write("Se ha cargado correctamente el auto.");
                return $response;
            }
            else
            {
                $response->getBody()->write("Error al cargar el auto.");
                return $response;
            }
        }
        else
        $response->getBody()->write("No hay espacios disponibles");
    }

    public function Egreso($request,$response)
    {
        $parametros = $request->getParsedBody();

        $fecha_salida=new DateTime();
        $fecha_ingreso=new DateTime($parametros["json"]["fecha_ingreso"]);
        $importe=Administracion::Importe($fecha_ingreso,$fecha_salida);
        $tiempo=($fecha_ingreso->diff($fecha_salida))->format("%H");

        $conexion=AccesoDatos::DameUnObjetoAcceso();
        $resultados = $conexion->RetornarConsulta("UPDATE `administracion` SET `id_empleado_salida`=".$parametros["id_empleado_salida"].", 
        `fecha_salida`='".$fecha_salida->format(DateTime::ATOM)."',`importe`='".$importe."',
        `tiempo`='".$tiempo."' WHERE `patente`='".$parametros["json"]["patente"]."' and `fecha_salida` IS NULL");
        
        if($resultados->execute())
        {
            $conexion=AccesoDatos::DameUnObjetoAcceso();
            $resultados = $conexion->RetornarConsulta("UPDATE `cocheras` SET `ocupada`=0 WHERE `id`=".$parametros['json']['id_cochera']);
            $resultados->execute();

            $response->getBody()->write('{"patente":"'.$parametros["json"]["patente"].'","tiempo":"'.$tiempo.'","importe":"'.$importe.'","fecha_salida":"'.$fecha_salida->format(DateTime::ATOM).'"}');
        }
        else
        return "ERROR";
        
    }

    private static function Importe($fechaIngreso , $fechaEgreso) {

        $diferencia = $fechaIngreso->diff($fechaEgreso);
        $horas = intval($diferencia->format("%H"));
        $minutos = intval($diferencia->format("%i"));

        $importe = 0;

        for($i=0 ; $i<$horas ; $i++) {

            $importe += 10;

            if($i==11) { $importe = 90; }
            else { if($i==23) { $importe = 170; } }
        }

        $importe += (10/60)*$minutos;

        return floatval(number_format($importe , 2));
    }
}