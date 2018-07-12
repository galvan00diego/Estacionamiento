<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once './vendor/autoload.php';
require_once './php/clases/Usuario.php';
require_once './php/clases/Middleware.php';
require_once './php/clases/Token.php';
require_once './php/clases/Administracion.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;


$app = new \Slim\App(["settings" => $config]);

/************************** LOGIN USER ******************************** */
$app->post("/login[/]" , function(Request $request , Response $response) 
    {
        $response=Usuario::Login($request , $response);
        return $response;
    });
/************************** REGISTRO USER ******************************** */
$app->post("/registro[/]" , function(Request $request , Response $response) 
{
    return Usuario::Registro($request , $response,$_FILES);
});
/************************** VERIFICO USER ******************************** */
$app->post("/verificar[/]" , function(Request $request , Response $response) 
{
    $response = Usuario::Verificar($request , $response);
    return json_encode($response);
});
/************************ ADMINISTRACION ************************************/
$app->group("/administracion" , function() {
    /**************************** INGRESO DE AUTOS *****************************/
    $this->post("/autoin[/]", function(Request $request , Response $response)
    {
        $response = Administracion::Ingreso($request,$response,$_FILES);
        return $response;
    });
    $this->post("/autoout[/]", function(Request $request , Response $response)
    {
        $response = Administracion::Egreso($request,$response);
        return $response;
        
        //var_dump($request->getParsedBody());
    });
});
/****************************************************************************/

/**************************** COCHERAS *****************************/
$app->group("/cochera" , function() {

    $this->get("/listar[/]" , function(Request $request , Response $response) {

        return Cochera::Listar($request , $response);
    });
    $this->get("/autos[/]" , function(Request $request , Response $response) {

        return Administracion::Listar($request , $response);
    });
});


$app->run();