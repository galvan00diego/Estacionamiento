<?php
require_once ("./vendor/autoload.php");
require_once 'AccesoDatos.php';

class Cochera
{

    private $_piso;
    private $_numero;
    private $_ocupada;
    private $_especial;

    public function __construct($piso,$numero,$ocupada,$especial)
    {
        $this->_piso=$piso;
        $this->_numero=$numero;
        $this->_ocupada=$ocupada;
    }

    public function getPiso(){return $this->_piso;}
    public function getNumero(){return $this->_numero;}
    public function getOcupada(){return $this->_ocupada;}
    public function getEspecial(){return $this->_especial;}

    public static function ToJson($obj)
    {
        return '{"piso":"'.$obj->getPiso().'", "numero":"'.$obj->getNumero().'" ,"ocupada": "'.$obj->getOcupada().'", "especial":"'.$obj->getEspecial().'"}';
    }

    public function Ocupada($numero)
    {
        $conexion=AccesoDatos::DameUnObjetoAcceso();
        $resultados = $conexion->RetornarConsulta("SELECT * FROM `cocheras` WHERE `numero`=$numero");

    }

    public function TraerLibre()
    {
        $conexion=AccesoDatos::DameUnObjetoAcceso();
        $resultados = $conexion->RetornarConsulta("SELECT * FROM `cocheras` WHERE `ocupada`=0");
        if($resultados->execute())
        {
            $fila = $resultados->fetch(PDO::FETCH_ASSOC);
            return $fila["id"];
        }
        else
        return FALSE;
    }

    public function Aparcar($id_cochera)
    {
        $conexion=AccesoDatos::DameUnObjetoAcceso();
        $resultados = $conexion->RetornarConsulta("UPDATE `cocheras` SET `ocupada`=1 WHERE `id`=$id_cochera");
        return $resultados->execute();
    }

    public function Listar()
    {
        $conexion=AccesoDatos::DameUnObjetoAcceso();
        $resultados = $conexion->RetornarConsulta("SELECT * FROM `cocheras`");
        $resultados->execute();

        $listado="[";

            while($fila = $resultados->fetch(PDO::FETCH_ASSOC)) 
            {
                $cochera=new Cochera($fila["piso"],$fila["numero"],$fila["ocupada"],$fila["especial"]);
               
                $listado.=Cochera::ToJson($cochera).",";
               
            }
            $listado=substr($listado,0,-1);
            $listado.="]";
            return $listado;
    }
}