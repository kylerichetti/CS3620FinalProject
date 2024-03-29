<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/12/2017
 * Time: 3:41 PM
 */

namespace BowlingBall\Controllers;

use \BowlingBall\Http\StatusCodes as StatusCodes;
use \BowlingBall\Models\Token as Token;
use \BowlingBall\Models\BowlingBall;

class BowlingBallsController
{
    //Get all active bowlingBalls
    public function getAllBowlingBalls($jwt = NULL)
    {
        $role = $this->extractRoleFromToken($jwt);
        if($role == Token::ROLE_DEV || $role == Token::ROLE_ADMIN) {
            $bowlingBalls = (new BowlingBall())->getAllBowlingBalls();
            if (!$bowlingBalls) {
                //Error, no bowlingBalls in database
                http_response_code(StatusCodes::NOT_FOUND);
                die("No bowlingBalls in database");
            }
            return $bowlingBalls;
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Get a single bowlingBall by ID
    public function getBowlingBallByID($bowlingBallID, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_DEV || $role == Token::ROLE_ADMIN) {
            $bowlingBall = new BowlingBall($bowlingBallID);

            if ($bowlingBall->getBowlingBallName() == NULL) {
                http_response_code(StatusCodes::NOT_FOUND);
                die("BowlingBall not found");
            }
            return $bowlingBall;
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Create a bowlingBall
    public function createBowlingBall($newBowlingBallData, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_ADMIN) {
            $bowlingBall = new BowlingBall();

            //Set attributes of model
            foreach ($newBowlingBallData as $atr => $value){
                $bowlingBall->setAtr($atr, $value);
            }

            //Try to insert new bowlingBall into database
            if ($bowlingBall->createBowlingBall()) {
                //Insert succeeded
                http_response_code(StatusCodes::CREATED);
                return $bowlingBall->JsonSerialize();
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
    //Update a bowlingBall
    public function updateBowlingBall($bowlingBallID, $updatedBowlingBallData, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_ADMIN) {
            $bowlingBall = new BowlingBall($bowlingBallID);

            //Check if bowlingBall exists
            if ($bowlingBall->getBowlingBallName() == NULL) {
                http_response_code(StatusCodes::NOT_FOUND);
                die("BowlingBall not found");
            }

            //Update model
            foreach ($updatedBowlingBallData as $atr => $value){
                $bowlingBall->setAtr($atr, $value);
            }
            //return $bowlingBall;
            //Update database
            if ($bowlingBall->updateBowlingBall()) {
                return $bowlingBall->JsonSerialize();
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
    //Soft delete a bowlingBall
    public function deleteBowlingBall($bowlingBallID, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_ADMIN) {
            $bowlingBall = new BowlingBall($bowlingBallID);
            if ($bowlingBall->deleteBowlingBall()) {
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

    private function extractRoleFromToken($jwt = NULL){
        try {
            $role = Token::getRoleFromToken($jwt);
        } catch (\Exception $err){
            $role = NULL;
        }
        return $role;
    }
}