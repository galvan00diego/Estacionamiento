<?php

require_once ("./vendor/autoload.php");
require_once ("AccesoDatos.php");
use Firebase\JWT\JWT;


class Token
{
private static $claveSecreta = 'zF9h5ghF5js3GPVa@';
private static $tipoEncriptacion = ['HS256'];
private static $aud = NULL;

    public static function JWT($datos)
    {
        ini_set('date.timezone','America/Argentina/Buenos_Aires'); 
        $fecha=date("Y-m-d H:i:s");
        $datos=json_decode($datos);
            $payload = array(
                'iat'=>$fecha,              //CUANDO SE CREO EL JWT (OPCIONAL)
                'aud' => self::Aud(),       //IDENTIFICA DESTINATARIOS (OPCIONAL)
                'id' => $datos->id,
                'nombre' => $datos->nombre,
                'email' => $datos->email,
                'clave' => $datos->clave,          //DATOS DEL JWT
                'foto' => $datos->foto,
                'app'=> "Estacionamiento 2017"     //INFO DE LA APLICACION (PROPIO)
            );
        
            //CODIFICO A JWT
            $token=JWT::encode($payload, self::$claveSecreta);

            return $token;
          
    }

    public static function Verificar($jwt)
    {
        
        if($jwt)
        {
            $token=JWT::decode($jwt,self::$claveSecreta,self::$tipoEncriptacion);
            return $token;
        }
        else
        {
           echo("Error al cargar el usuario.");
            return FALSE;
        }
    }

    private static function Aud() 
    {
        
        $aud = '';
                
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {
             $aud = $_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        else 
        {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
                
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
    }
}