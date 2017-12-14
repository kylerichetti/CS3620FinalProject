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
        $data = array();
        if (!empty($_POST['brandName'])) {
            $data['brandName'] = filter_var($_POST['brandName'], FILTER_SANITIZE_STRING);
        }
        else{
            //Attempt to parse json input
            $json = (object) json_decode(file_get_contents('php://input'));
            if (count((array)$json) >= 1) {
                $data['brandName'] = filter_var($json->brandName, FILTER_SANITIZE_STRING);
            } else {
                http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
                exit();
            }
        }
        return $brandCtrl->createBrand($data);
    };
    $updateBrand = function($args)
    {
        $brandCtrl = new BrandsController();
        $data = array();
        //Attempt to parse json input
        $json = (object) json_decode(file_get_contents('php://input'));
        if (count((array)$json) >= 1) {
            $data['brandName'] = filter_var($json->brandName, FILTER_SANITIZE_STRING);
        } else {
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            exit();
        }

        return $brandCtrl->updateBrand($args['id'], $data);
    };
    $deleteBrand = function($args)
    {
        $brandCtrl = new BrandsController();
        return  $brandCtrl->deleteBrand($args['id']);

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
        $data = array();
        if (!empty($_POST['coreTypeName'])) {
            $data['coreTypeName'] = filter_var($_POST['coreTypeName'], FILTER_SANITIZE_STRING);
        }
        else{
            //Attempt to parse json input
            $json = (object) json_decode(file_get_contents('php://input'));
            if (count((array)$json) >= 1) {
                $data['coreTypeName'] = filter_var($json->coreTypeName, FILTER_SANITIZE_STRING);
            } else {
                http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
                exit();
            }
        }

        return $coreCtrl->createCore($data);
    };
    $updateCore = function($args)
    {
        $coreCtrl = new CoresController();
        $data = array();
        //Attempt to parse json input
        $json = (object) json_decode(file_get_contents('php://input'));
        if (count((array)$json) >= 1) {
            $data['coreTypeName'] = filter_var($json->coreTypeName, FILTER_SANITIZE_STRING);
        } else {
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            exit("API");
        }

        return $coreCtrl->updateCore($args['id'], $data);
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
        $data = array();
        if (!empty($_POST['coverstockTypeName'])) {
            $data['coverstockTypeName'] = filter_var($_POST['coverstockTypeName'], FILTER_SANITIZE_STRING);
        }
        else{
            //Attempt to parse json input
            $json = (object) json_decode(file_get_contents('php://input'));
            if (count((array)$json) >= 1) {
                $data['coverstockTypeName'] = filter_var($json->coverstockTypeName, FILTER_SANITIZE_STRING);
            } else {
                http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
                exit();
            }
        }
        return $coverstockCtrl->createCoverstock($data);
    };
    $updateCoverstock = function($args)
    {
        $coverstockCtrl = new CoverstocksController();
        $data = array();
        //Attempt to parse json input
        $json = (object) json_decode(file_get_contents('php://input'));
        if (count((array)$json) >= 1) {
           $data['coverstockTypeName'] = filter_var($json->coverstockTypeName, FILTER_SANITIZE_STRING);
        } else {
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            exit();
        }

        return $coverstockCtrl->updateCoverstock($args['id'], $data);
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
        $data = array();
        if (
            !empty($_POST['bowlingBallName']) &&
            !empty($_POST['brandName']) &&
            !empty($_POST['coreTypeName']) &&
            !empty($_POST['coverstockTypeName'])
        )
        {
            $data['bowlingBallName'] = filter_var($_POST['bowlingBallName'], FILTER_SANITIZE_STRING);
            $data['brandName'] = filter_var($_POST['brandName'], FILTER_SANITIZE_STRING);
            $data['coreTypeName'] = filter_var($_POST['coreTypeName'], FILTER_SANITIZE_STRING);
            $data['coverstockTypeName'] = filter_var($_POST['coverstockTypeName'], FILTER_SANITIZE_STRING);
        }
        else
        {
            //Attempt to parse json input
            $json = (object) json_decode(file_get_contents('php://input'));
            if (count((array)$json) >= 4) {
                $data['bowlingBallName'] = filter_var($json->bowlingBallName, FILTER_SANITIZE_STRING);
                $data['brandName'] = filter_var($json->brandName, FILTER_SANITIZE_STRING);
                $data['coreTypeName'] = filter_var($json->coreTypeName, FILTER_SANITIZE_STRING);
                $data['coverstockTypeName'] = filter_var($json->coverstockTypeName, FILTER_SANITIZE_STRING);
            } else {
                http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
                exit();
            }
        }

        return $bowlingBallCtrl->createBowlingBall($data);
    };
    $updateBowlingBall = function($args)
    {
        $bowlingBallCtrl = new BowlingBallsController();
        $data = array();
        //Attempt to parse json input
        $json = (object) json_decode(file_get_contents('php://input'));
        if (count((array)$json) >= 4) {
            $data['bowlingBallName'] = filter_var($json->bowlingBallName, FILTER_SANITIZE_STRING);
            $data['brandName'] = filter_var($json->brandName, FILTER_SANITIZE_STRING);
            $data['coreTypeName'] = filter_var($json->coreTypeName, FILTER_SANITIZE_STRING);
            $data['coverstockTypeName'] = filter_var($json->coverstockTypeName, FILTER_SANITIZE_STRING);
        } else {
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            exit();
        }

        return $bowlingBallCtrl->updateBowlingBall($args['id'], $data);
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











