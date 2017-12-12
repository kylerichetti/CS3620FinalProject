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

class BrandsController
{
    //Get a single brand by ID
    public function getBrandByID($brandID){
        $brand = new Brand($brandID);

        if($brand->getBrandName() == -1){
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Brand not found");
        }

        return $brand;
    }
    //Get all active brands
    public function getAllBrands()
    {
        $brands = (new Brand())->getAllBrands();
        if($brands == -1)
        {
            //Error, no brands in database
            http_response_code(StatusCodes::BAD_REQUEST);
            die("No brands in database");
        }
        return $brands;
    }
    //Create a brand
    public function createBrand($newBrandData){
        //Permissions test

        $brand = new Brand(0);
        $newBrandName = $newBrandData['brandName'];

        //Try to set name of the new brand
        if($brand->setBrandName($newBrandName) == -1){
            //Error, tried to pass bad data as a brand name
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Invalid brand name");
        }

        //Try to insert new brand into database
        if($brand->createBrand()){
            //Insert succeeded
            http_response_code(StatusCodes::CREATED);
            return $brand->JsonSerialize();
        }
        else{
            //Insert failed
            //TODO: Double check proper response code
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Insert failed");
        }
    }
    //Update a brand
    public function updateBrand($brandID, $updatedBrandData){
        //Permissions test

        $brand = new Brand($brandID);
        $newBrandName = $updatedBrandData['brandName'];

        //Check if brand exists
        if($brand->getBrandName() == -1){
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Brand not found");
        }

        //Update database
        if($brand->updateBrand($newBrandName))
        {
            http_response_code(StatusCodes::OK);
            return $brand->JsonSerialize();
        }
        else{
            //Response code?
            http_response_code(StatusCodes::NOT_MODIFIED);
            die("Update failed");
        }
    }
    //Soft delete a brand
    public function deleteBrand($brandID){
        //Permissions test

        $brand = new Brand($brandID);
        if($brand->deleteBrand() == 1){
            //Response code?
            http_response_code(StatusCodes::OK);
            return "Success";
        }
        else{
            //Response code?
            http_response_code(StatusCodes::NOT_MODIFIED);
            die("Delete failed");
        }
    }
}