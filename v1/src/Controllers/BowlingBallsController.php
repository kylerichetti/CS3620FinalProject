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
    public function getAllBowlingBalls()
    {
        $role = Token::getRoleFromToken();
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
    public function getBowlingBallByID($bowlingBallID){
        $role = Token::getRoleFromToken();
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
    public function createBowlingBall($newBowlingBallData){
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_ADMIN) {
            $bowlingBall = new BowlingBall();
            $newBowlingBallName = $newBowlingBallData['bowlingBallName'];

            //Set name of the new bowlingBall
            $bowlingBall->setBowlingBallName($newBowlingBallName);

            //Check if Brand name given is invalid while attempting to set it
            if(!$bowlingBall->setBowlingBallBrand($newBowlingBallData['brandName'])){
                http_response_code(StatusCodes::BAD_REQUEST);
                die("Invalid Brand");
            }

            //Check if Core Type name given is invalid while attempting to set it
            if(!$bowlingBall->setBowlingBallCore($newBowlingBallData['coreTypeName'])){
                http_response_code(StatusCodes::BAD_REQUEST);
                die("Invalid Core Type");
            }

            //Check if Core Type name given is invalid while attempting to set it
            if(!$bowlingBall->setBowlingBallCoverstock($newBowlingBallData['coverstockTypeName'])){
                http_response_code(StatusCodes::BAD_REQUEST);
                die("Invalid Coverstock Type");
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
    public function updateBowlingBall($bowlingBallID, $updatedBowlingBallData){
        //Permissions test
        $role = Token::getRoleFromToken();
        if($role == Token::ROLE_ADMIN) {

            $bowlingBall = new BowlingBall($bowlingBallID);
            $newBowlingBallName = $updatedBowlingBallData['bowlingBallName'];

            //Check if bowlingBall exists
            if ($bowlingBall->getBowlingBallName() == NULL) {
                http_response_code(StatusCodes::NOT_FOUND);
                die("BowlingBall not found");
            }

            //Update database
            if ($bowlingBall->updateBowlingBall($newBowlingBallName)) {
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
    public function deleteBowlingBall($bowlingBallID){
        //Permissions test
        $role = Token::getRoleFromToken();
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
}