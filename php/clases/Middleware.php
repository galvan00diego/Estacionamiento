<?php

require_once "Usuario.php";

class Middleware
{
    public static function log($request,$response,$next)
    {
        $ruta="./log/productos-api.txt";
    
        if(file_exists($ruta))
        {
            $arc=fopen($ruta,"r");
            $cant=fread($arc,filesize($ruta));
            fclose($arc);
            $cant=substr($cant,-1);
            
            $cant+=1;
            $arc=fopen( $ruta,"w");
            $cant=fwrite($arc,"Cantidad de veces ingresadas al api productos: ".$cant);
            fclose($arc);
        }
        else
        {
            $arc=fopen( $ruta,"w");
            $cant=fwrite($arc,"Cantidad de veces ingresadas al api: 1");
            fclose($arc);
        }
        $response=$next($request,$response);
        return $response;
    }

    public static function logNombreApellido($request,$response,$next)
    {
        $ruta="./log/productos-post.txt";
        $datos=json_decode(Empleado::Verificar($request,$response));
        ini_set('date.timezone','America/Argentina/Buenos_Aires'); 
        $fecha=date("Y-m-d H:i:s");

        if(file_exists($ruta))
        {
            $arc=fopen( $ruta,"a");
            $cant=fwrite($arc,$datos->usuario->nombre." ".$datos->usuario->apellido." ".$fecha."\n");
            fclose($arc);
        }
        else
        {
            $arc=fopen( $ruta,"w");
            $cant=fwrite($arc,$datos->usuario->nombre." ".$datos->usuario->apellido." ".$fecha."\n");
            fclose($arc);
        }
        $response=$next($request,$response);
        return $response;
    }
}