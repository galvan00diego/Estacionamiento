<?php

require_once ("./vendor/autoload.php");
require_once ("Token.php");
require_once ("AccesoDatos.php");

    class Usuario
    {

        private $_id;
        private $_nombre;
        private $_estado;
        private $_email;
        private $_foto;
        private $_turno;
        private $_sexo;
        private $_clave;
        private $_perfil;

        public function GetId(){return $this->_id;}
        public function GetNombre(){return $this->_nombre;}
        public function GetClave(){return $this->_clave;}
        public function GetEmail(){return $this->_email;}
        public function GetTurno(){return $this->_turno;}
        public function GetSexo(){return $this->_sexo;}
        public function GetFoto(){return $this->_foto;}
        public function GetEstado(){return $this->_estado;}
        public function GetPerfil(){return $this->_perfil;}

        public function __construct($id=NULL,$nombre,$clave,$email,$turno,$sexo,$foto,$estado,$perfil) {

            $this->_id=$id;
            $this->_nombre = $nombre;
            $this->_clave=$clave;
            $this->_email=$email;
            $this->_turno = $turno;
            $this->_sexo=$sexo;
            $this->_foto = $foto;
            $this->_estado = $estado;
            $this->_perfil = $perfil;
            
        }

        public static function ToJson($obj) {

            return '{"id":"'.$obj->GetId().'", "nombre":"'.$obj->GetNombre().'" ,"clave": "'.$obj->GetClave().'", "email":"'.$obj->GetEmail().'","turno":"'.$obj->GetTurno().
                '","sexo":"'.$obj->GetSexo().'","foto":"'.$obj->GetFoto().'","estado":"'.$obj->GetEstado().'","perfil":"'.$obj->GetPerfil().'"}';
        }

        public static function Login($request,$response)
        {
            $parametros = $request->getParsedBody();// RECIBO PARAMETROS
            
            $conexion=AccesoDatos::DameUnObjetoAcceso();// CREO CONEXION A LA BASE DE DATOS
            $resultados = $conexion->RetornarConsulta("SELECT * FROM `empleados` WHERE `email`='".$parametros["loginemail"]."' AND `clave`='".$parametros["loginpass"]."'");
            $resultados->execute();
            
            $fila = $resultados->fetch(PDO::FETCH_ASSOC);
            
            if($fila)
            {
                $datos='{"id":"'.$fila["id"].'","nombre":"'.$fila["nombre"].'","email":"'.$fila["email"].'","clave":"'.$fila["clave"].'","foto":"'.$fila["foto"].'"}';
                $token=Token::JWT($datos);
                return $token;
                
            }
            else
            {
                return FALSE;
            }
        }

        public static function Registro($request,$response,$foto)
        {
            $parametros = $request->getParsedBody();
            $fotoFinal = date("Gis").".".pathinfo($foto["foto"]["name"] , PATHINFO_EXTENSION);
            $rutaFoto = __DIR__."./img/".$fotoFinal;

            $empleado=new Usuario(NULL,$parametros["nombre"],$parametros["clave"],$parametros["email"],$parametros["turno"],$parametros["sexo"],$fotoFinal,1,"empleado");
           
            if(move_uploaded_file($foto["foto"]["tmp_name"] , $rutaFoto))
                $response->getBody()->write("La foto se cargo correctamente.");
            else
                $response->getBody()->write("Error al cargar la foto.");

            $conexion=AccesoDatos::DameUnObjetoAcceso();
            $resultados = $conexion->RetornarConsulta("INSERT INTO `empleados`(`nombre`, `clave`, `email`, `turno`, `sexo`, `foto`, `estado`, `perfil`)VALUES ('".$empleado->GetNombre()."' , '".$empleado->GetClave()."' ,'".$empleado->GetEmail()."' ,'".$empleado->GetTurno()."' ,'".$empleado->GetSexo()."' , '".$fotoFinal." ','".$empleado->GetEstado()."','".$empleado->GetPerfil()."')");
            if($resultados->execute())
            {    
                $response->getBody()->write("Se ha cargado correctamente el nuevo usuario.");
                return $response;
            }
            else
            {
                $response->getBody()->write("Error al cargar el usuario.");
                return $response;
            }
        }

        public static function Verificar($request,$response)
        {
            $param=$request->getParsedBody();
            $tokendecoded=Token::Verificar($param["token"]);
            return $tokendecoded;
        }
}