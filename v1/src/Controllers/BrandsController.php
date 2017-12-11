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
    public function getBrandByID($brandID){
        $brand = new Brand($brandID);

        if($brand->getBrandName() == -1){
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Brand not found");
        }

        return $brand;
    }

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
        $brand = new Brand(0);
        $newBrandName = $newBrandData['brandName'];

        //Try to set name of the new brand
        if($brand->setBrandName($newBrandName) == -1){
            //Error, tried to pass bad data as a brand name
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Invalid brand name");
        }

        //Try to insert new brand into database
        if($brand->createBrand() == 1){
            //Insert succeeded
            http_response_code(StatusCodes::CREATED);
        }
        else{
            //Insert failed
            //TODO: Double check proper response code
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Insert failed");
        }

        return "Success";
    }
    //Update a brand
    public function updateBrand($brandID, $updatedBrandData){
        $brand = new Brand($brandID);

        //var_dump($updatedBrandData);
        $newBrandName = $updatedBrandData['brandName'];

        //Check if brand exists
        if($brand->getBrandName() == -1){
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Brand not found");
        }
        //Change brand model
        /*if($brand->setBrandName($newBrandName) == -1){
            //Error, tried to pass bad data as a brand name
            http_response_code(StatusCodes::BAD_REQUEST);
            die("Invalid brand name");
        }*/
        //Update database
        if($brand->updateBrand($newBrandName) == 1)
        {
            //Response code?
            http_response_code(StatusCodes::OK);
            return "Success";
        }
        else{
            //Response code?
            http_response_code(StatusCodes::NOT_MODIFIED);
            die("Update failed");
        }
    }
    //Soft delete a brand
    public function deleteBrand(){

    }
}