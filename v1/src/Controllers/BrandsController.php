<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/10/2017
 * Time: 3:46 PM
 */

namespace BowlingBall\Controllers;

use \BowlingBall\Http\StatusCodes;
use \BowlingBall\Models\Token as Token;
use \BowlingBall\Models\Brand;

class BrandsController
{
    public function getBrandByID($brandID){
        $brand = new Brand($brandID);

        /*if($brand == -1){
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            die("Brand not found");
        }*/

        return $brand;
    }

    public function getAllBrands()
    {
        $brands = (new Brand())->getAllBrands();
        if($brands == -1)
        {
            //Error, no brands in database
            http_response_code(\BowlingBall\Http\StatusCodes::BAD_REQUEST);
            die("No brands in database");
        }
        return $brands;
    }
}