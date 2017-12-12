<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/12/2017
 * Time: 2:47 PM
 */

namespace BowlingBall\Controllers;

use \BowlingBall\Http\StatusCodes as StatusCodes;
use \BowlingBall\Models\Token as Token;
use \BowlingBall\Models\Core;

class CoresController
{
    //Get all active cores
    public function getAllCores()
    {
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_DEV || $role == Token::ROLE_ADMIN) {
            $cores = (new Core())->getAllCores();
            if (!$cores) {
                //Error, no cores in database
                http_response_code(StatusCodes::NOT_FOUND);
                die("No cores in database");
            }
            return $cores;
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Get a single core by ID
    public function getCoreByID($coreTypeID){
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_DEV || $role == Token::ROLE_ADMIN) {
            $core = new Core($coreTypeID);

            if ($core->getCoreTypeName() == NULL) {
                http_response_code(StatusCodes::NOT_FOUND);
                die("Core not found");
            }
            return $core;
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Create a core
    public function createCore($newCoreData){
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_ADMIN) {
            $core = new Core();
            $newCoreName = $newCoreData['coreTypeName'];

            //Set name of the new core
            $core->setCoreTypeName($newCoreName);

            //Try to insert new core into database
            if ($core->createCore()) {
                //Insert succeeded
                http_response_code(StatusCodes::CREATED);
                return $core->JsonSerialize();
            } else {
                //Insert failed
                http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
                die("Insert failed");
            }
        }
        elseif ($role == Token::ROLE_DEV){
            //Forbidden
            http_response_code(StatusCodes::FORBIDDEN);
            die("Improper Role");
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Update a core
    public function updateCore($coreTypeID, $updatedCoreData){
        //Permissions test
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_ADMIN) {

            $core = new Core($coreTypeID);
            $newCoreName = $updatedCoreData['coreTypeName'];

            //Check if core exists
            if ($core->getCoreTypeName() == NULL) {
                http_response_code(StatusCodes::NOT_FOUND);
                die("Core not found");
            }

            //Update database
            if ($core->updateCore($newCoreName)) {
                return $core->JsonSerialize();
            }
            else {
                http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
                die("Update failed");
            }
        }
        elseif ($role == Token::ROLE_DEV){
            //Forbidden
            http_response_code(StatusCodes::FORBIDDEN);
            die("Improper Role");
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Soft delete a core
    public function deleteCore($coreTypeID){
        //Permissions test
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_ADMIN) {
            $core = new Core($coreTypeID);
            if ($core->deleteCore()) {
                http_response_code(StatusCodes::OK);
                return "Success";
            }
            else {
                http_response_code(StatusCodes::INTERNAL_SERVER_ERROR);
                die("Delete failed");
            }
        }
        elseif ($role == Token::ROLE_DEV){
            //Forbidden
            http_response_code(StatusCodes::FORBIDDEN);
            die("Improper Role");
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
}