<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/12/2017
 * Time: 3:20 PM
 */

namespace BowlingBall\Controllers;

use \BowlingBall\Http\StatusCodes as StatusCodes;
use \BowlingBall\Models\Token as Token;
use \BowlingBall\Models\Coverstock;

class CoverstocksController
{
    //Get all active coverstocks
    public function getAllCoverstocks($jwt = NULL)
    {
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_DEV || $role == Token::ROLE_ADMIN) {
            $coverstocks = (new Coverstock())->getAllCoverstocks();
            if (!$coverstocks) {
                //Error, no coverstocks in database
                http_response_code(StatusCodes::NOT_FOUND);
                die("No coverstocks in database");
            }
            return $coverstocks;
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Get a single coverstock by ID
    public function getCoverstockByID($coverstockTypeID, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_DEV || $role == Token::ROLE_ADMIN) {
            $coverstock = new Coverstock($coverstockTypeID);

            if ($coverstock->getCoverstockTypeName() == NULL) {
                http_response_code(StatusCodes::NOT_FOUND);
                die("Coverstock not found");
            }
            return $coverstock;
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Create a coverstock
    public function createCoverstock($newCoverstockData, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_ADMIN) {
            $coverstock = new Coverstock();

            //Set name of the new coverstock
            foreach ($newCoverstockData as $atr => $value){
                if(!empty($atr) && !empty($value)) {
                    $coverstock->setAtr($atr, $value);
                }
                else{
                    http_response_code(StatusCodes::BAD_REQUEST);
                    die("Invalid data in request body");
                }
            }

            //Try to insert new coverstock into database
            if ($coverstock->createCoverstock()) {
                //Insert succeeded
                http_response_code(StatusCodes::CREATED);
                return $coverstock->JsonSerialize();
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
    //Update a coverstock
    public function updateCoverstock($coverstockTypeID, $updatedCoverstockData, $jwt = NULL){
        //Permissions test
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_ADMIN) {

            $coverstock = new Coverstock($coverstockTypeID);

            //Check if coverstock exists
            if ($coverstock->getCoverstockTypeName() == NULL) {
                http_response_code(StatusCodes::NOT_FOUND);
                die("Coverstock not found");
            }

            //Update model
            foreach ($updatedCoverstockData as $atr => $value){
                if(!empty($atr) && !empty($value)) {
                    $coverstock->setAtr($atr, $value);
                }
                else{
                    http_response_code(StatusCodes::BAD_REQUEST);
                    die("Invalid data in request body");
                }
            }

            //Update database
            if ($coverstock->updateCoverstock()) {
                return $coverstock->JsonSerialize();
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
    //Soft delete a coverstock
    public function deleteCoverstock($coverstockTypeID, $jwt = NULL){
        //Permissions test
        $role = $this->extractRoleFromToken($jwt);
        if($role == Token::ROLE_ADMIN) {
            $coverstock = new Coverstock($coverstockTypeID);
            if ($coverstock->deleteCoverstock()) {
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

    //Extract role
    private function extractRoleFromToken($jwt = NULL){
        try {
            $role = Token::getRoleFromToken($jwt);
        } catch (\Exception $err){
            $role = NULL;
        }
        return $role;
    }
}