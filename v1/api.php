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
use \BowlingBall\Controllers\CoresController as CoresController;
use \BowlingBall\Controllers\CoverstocksController as CoverstocksController;
use \BowlingBall\Controllers\BowlingBallsController as BowlingBallsController;


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
        $brandCtrl = new BrandsController();

        $val = $brandCtrl->getAllBrands();

        return $val;
    };
    $getBrandByID = function ($args)
    {
        $brandCtrl = new BrandsController();

        $val = $brandCtrl->getBrandByID($args['id']);

        return $val;
    };
    $createBrand = function ($args)
    {
        $brandCtrl = new BrandsController();
        $json = array();
        if (!empty($_POST['brandName'])) {
            $json['brandName'] = filter_var($_POST['brandName'], FILTER_SANITIZE_STRING);
        }
        else{
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            die("Error: No body in POST");
        }

        return $brandCtrl->createBrand($json);
    };
    $updateBrand = function($args)
    {
        $brandCtrl = new BrandsController();

        parse_str(file_get_contents('php://input'), $json);

        return $brandCtrl->updateBrand($args['id'], $json);
    };
    $deleteBrand = function($args)
    {
        $brandCtrl = new BrandsController();
        return $brandCtrl->deleteBrand($args['id']);
    };

    /*Core Closures*/
    $getAllCores = function ($args)
    {
        $coreCtrl = new CoresController();

        $val = $coreCtrl->getAllCores();

        return $val;
    };
    $getCoreByID = function ($args)
    {
        $coreCtrl = new CoresController();

        $val = $coreCtrl->getCoreByID($args['id']);

        return $val;
    };
    $createCore = function ($args)
    {
        $coreCtrl = new CoresController();
        $json = array();
        if (!empty($_POST['coreTypeName'])) {
            $json['coreTypeName'] = filter_var($_POST['coreTypeName'], FILTER_SANITIZE_STRING);
        }
        else{
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            die("Error: No body in POST");
        }

        return $coreCtrl->createCore($json);
    };
    $updateCore = function($args)
    {
        $coreCtrl = new CoresController();

        parse_str(file_get_contents('php://input'), $json);

        return $coreCtrl->updateCore($args['id'], $json);
    };
    $deleteCore = function($args)
    {
        $coreCtrl = new CoresController();
        return $coreCtrl->deleteCore($args['id']);
    };

    /*Coverstock Closures*/
    $getAllCoverstocks = function ($args)
    {
        $coverstockCtrl = new CoverstocksController();

        $val = $coverstockCtrl->getAllCoverstocks();

        return $val;
    };
    $getCoverstockByID = function ($args)
    {
        $coverstockCtrl = new CoverstocksController();

        $val = $coverstockCtrl->getCoverstockByID($args['id']);

        return $val;
    };
    $createCoverstock = function ($args)
    {
        $coverstockCtrl = new CoverstocksController();
        $json = array();
        if (!empty($_POST['coverstockTypeName'])) {
            $json['coverstockTypeName'] = filter_var($_POST['coverstockTypeName'], FILTER_SANITIZE_STRING);
        }
        else{
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            die("Error: No body in POST");
        }

        return $coverstockCtrl->createCoverstock($json);
    };
    $updateCoverstock = function($args)
    {
        $coverstockCtrl = new CoverstocksController();

        parse_str(file_get_contents('php://input'), $json);

        return $coverstockCtrl->updateCoverstock($args['id'], $json);
    };
    $deleteCoverstock = function($args)
    {
        $coverstockCtrl = new CoverstocksController();
        return $coverstockCtrl->deleteCoverstock($args['id']);
    };

    /*BowlingBall Closures*/
    $getAllBowlingBalls = function ($args)
    {
        $bowlingBallCtrl = new BowlingBallsController();

        $val = $bowlingBallCtrl->getAllBowlingBalls();

        return $val;
    };
    $getBowlingBallByID = function ($args)
    {
        $bowlingBallCtrl = new BowlingBallsController();

        $val = $bowlingBallCtrl->getBowlingBallByID($args['id']);

        return $val;
    };
    $createBowlingBall = function ($args)
    {
        $bowlingBallCtrl = new BowlingBallsController();
        $json = array();
        if (!empty($_POST['bowlingBallName'])) {
            $json['bowlingBallName'] = filter_var($_POST['bowlingBallName'], FILTER_SANITIZE_STRING);
        }
        else{
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            die("Error: No ball name in POST");
        }

        if (!empty($_POST['brandName'])) {
            $json['brandName'] = filter_var($_POST['brandName'], FILTER_SANITIZE_STRING);
        }
        else{
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            die("Error: No brand name in POST");
        }

        if (!empty($_POST['coreTypeName'])) {
            $json['coreTypeName'] = filter_var($_POST['coreTypeName'], FILTER_SANITIZE_STRING);
        }
        else{
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            die("Error: No core type in POST");
        }

        if (!empty($_POST['coverstockTypeName'])) {
            $json['coverstockTypeName'] = filter_var($_POST['coverstockTypeName'], FILTER_SANITIZE_STRING);
        }
        else{
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            die("Error: No coverstock  type in POST");
        }

        return $bowlingBallCtrl->createBowlingBall($json);
    };
    $updateBowlingBall = function($args)
    {
        $bowlingBallCtrl = new BowlingBallsController();

        parse_str(file_get_contents('php://input'), $json);
        foreach ($json as $key => $value){
            $value = filter_var($value, FILTER_SANITIZE_STRING);
        }

        return $bowlingBallCtrl->updateBowlingBall($args['id'], $json);
    };
    $deleteBowlingBall = function($args)
    {
        $bowlingBallCtrl = new BowlingBallsController();
        return $bowlingBallCtrl->deleteBowlingBall($args['id']);
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

    /*Core Routes*/
    $r->addRoute(Methods::GET, $baseURI . '/cores', $getAllCores);
    $r->addRoute(Methods::GET, $baseURI . '/cores/{id:\d+}', $getCoreByID);
    $r->addRoute(Methods::POST, $baseURI . '/cores', $createCore);
    $r->addRoute(Methods::PATCH, $baseURI . '/cores/{id:\d+}', $updateCore);
    $r->addRoute(Methods::DELETE, $baseURI . '/cores/{id:\d+}', $deleteCore);

    /*Coverstock Routes*/
    $r->addRoute(Methods::GET, $baseURI . '/coverstocks', $getAllCoverstocks);
    $r->addRoute(Methods::GET, $baseURI . '/coverstocks/{id:\d+}', $getCoverstockByID);
    $r->addRoute(Methods::POST, $baseURI . '/coverstocks', $createCoverstock);
    $r->addRoute(Methods::PATCH, $baseURI . '/coverstocks/{id:\d+}', $updateCoverstock);
    $r->addRoute(Methods::DELETE, $baseURI . '/coverstocks/{id:\d+}', $deleteCoverstock);

    /*BowlingBall Routes*/
    $r->addRoute(Methods::GET, $baseURI . '/bowlingBalls', $getAllBowlingBalls);
    $r->addRoute(Methods::GET, $baseURI . '/bowlingBalls/{id:\d+}', $getBowlingBallByID);
    $r->addRoute(Methods::POST, $baseURI . '/bowlingBalls', $createBowlingBall);
    $r->addRoute(Methods::PATCH, $baseURI . '/bowlingBalls/{id:\d+}', $updateBowlingBall);
    $r->addRoute(Methods::DELETE, $baseURI . '/bowlingBalls/{id:\d+}', $deleteBowlingBall);

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











