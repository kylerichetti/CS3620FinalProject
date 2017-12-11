<?php
/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 * Date: 10/13/2016
 * Time: 8:56 AM
 */

require_once 'config.php';
require_once 'vendor/autoload.php';
use \BowlingBall\Http\Methods as Methods;
use \BowlingBall\Controllers\BrandsController as BrandsController;


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r)  use ($baseURI) {
    /** TOKENS CLOSURES */
    $handlePostToken = function ($args) {
        $tokenController = new \BowlingBall\Controllers\TokensController();
        //Is the data via a form?
        if (!empty($_POST['username'])) {
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? "";
        } else {
            //Attempt to parse json input
            $json = (object) json_decode(file_get_contents('php://input'));
            if (count((array)$json) >= 2) {
                $username = filter_var($json->username, FILTER_SANITIZE_STRING);
                $password = $json->password;
            } else {
                http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
                exit();
            }
        }
        return $tokenController->buildToken($username, $password);

    };

    /*Brand Closures*/
    $getAllBrands = function ($args)
    {
        $brand = new BrandsController();

        $val = $brand->getAllBrands();

        return $val;
    };
    $getBrandByID = function ($args)
    {
        $brand = new BrandsController();

        $val = $brand->getBrandByID($args['id']);

        return $val;
    };
    $createBrand = function ($args)
    {
        $brand = new BrandsController();

        $json = $_POST;

        return $brand->createBrand($json);
    };
    $updateBrand = function($args){
        $brand = new BrandsController();

        //$json = (array) json_decode(file_get_contents('php://input'));
        parse_str(file_get_contents('php://input'), $json);

        return $brand->updateBrand($args['id'], $json);
    };
    $deleteBrand = function($args){

    };

    /*Routes*/

    /*Token Routes*/
    $r->addRoute(Methods::POST, $baseURI . '/tokens', $handlePostToken);

    /*Brand Routes*/
    $r->addRoute(Methods::GET, $baseURI . '/brands', $getAllBrands);
    $r->addRoute(Methods::GET, $baseURI . '/brands/{id:\d+}', $getBrandByID);
    $r->addRoute(Methods::POST, $baseURI . '/brands', $createBrand);
    $r->addRoute(Methods::PATCH, $baseURI . '/brands/{id:\d+}', $updateBrand);
    $r->addRoute(Methods::DELETE, $baseURI . '/brands/{id:\d+}', $deleteBrand);

});

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$pos = strpos($uri, '?');
if ($pos !== false) {
    $uri = substr($uri, 0, $pos);
}
$uri = rtrim($uri, "/");

$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($method, $uri);

switch($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(BowlingBall\Http\StatusCodes::NOT_FOUND);
        //Handle 404
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(BowlingBall\Http\StatusCodes::METHOD_NOT_ALLOWED);
        //Handle 403
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler  = $routeInfo[1];
        $vars = $routeInfo[2];

        $response = $handler($vars);
        echo json_encode($response);
        break;
}











