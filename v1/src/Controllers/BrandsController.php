<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/10/2017
 * Time: 3:46 PM
 */

namespace BowlingBall\Controllers;

use \BowlingBall\Http\StatusCodes as StatusCodes;
use \BowlingBall\Models\Token as Token;
use \BowlingBall\Models\Brand;
use TheSeer\Tokenizer\TokenCollectionException;

class BrandsController
{
    //Get all active brands
    public function getAllBrands($jwt = NULL)
    {
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_DEV || $role == Token::ROLE_ADMIN) {
            $brands = (new Brand())->getAllBrands();
            if (!$brands) {
                //Error, no brands in database
                http_response_code(StatusCodes::NOT_FOUND);
                die("No brands in database");
            }
            return $brands;
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Get a single brand by ID
    public function getBrandByID($brandID, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_DEV || $role == Token::ROLE_ADMIN) {
            $brand = new Brand($brandID);

            if ($brand->getBrandName() == NULL) {
                http_response_code(StatusCodes::NOT_FOUND);
                die("Brand not found");
            }
            http_response_code(StatusCodes::OK);
            return $brand;
        }
        else{
            //unauthorized
            http_response_code(StatusCodes::UNAUTHORIZED);
            die("No token provided");
        }
    }
    //Create a brand
    public function createBrand($newBrandData, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);
        if($role == Token::ROLE_ADMIN) {
            $brand = new Brand();

            //Set attributes of the new brand
            foreach ($newBrandData as $atr => $value){
                if(!empty($atr) && !empty($value)) {
                    $brand->setAtr($atr, $value);
                }
                else{
                    http_response_code(StatusCodes::BAD_REQUEST);
                    die("Invalid data in request body");
                }
            }

            //Try to insert new brand into database
            if ($brand->createBrand()) {
                //Insert succeeded
                http_response_code(StatusCodes::CREATED);
                return $brand->JsonSerialize();
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
    //Update a brand
    public function updateBrand($brandID, $updatedBrandData, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_ADMIN) {

            $brand = new Brand($brandID);

            //Check if brand exists
            if ($brand->getBrandName() == NULL) {
                http_response_code(StatusCodes::NOT_FOUND);
                die("Brand not found");
            }

            //Update model
            foreach ($updatedBrandData as $atr => $value){
                if(!empty($atr) && !empty($value)) {
                    $brand->setAtr($atr, $value);
                }
                else{
                    http_response_code(StatusCodes::BAD_REQUEST);
                    die("Invalid data in request body");
                }
            }

            //Update database
            if ($brand->updateBrand()) {
                return $brand->JsonSerialize();
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
    //Soft delete a brand
    public function deleteBrand($brandID, $jwt = NULL){
        $role = $this->extractRoleFromToken($jwt);

        if($role == Token::ROLE_ADMIN) {
            $brand = new Brand($brandID);
            if ($brand->deleteBrand()) {
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