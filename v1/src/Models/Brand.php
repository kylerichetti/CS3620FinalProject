<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/10/2017
 * Time: 3:24 PM
 */

namespace BowlingBall\Models;

use \BowlingBall\Utilities\DatabaseConnection as dbConnection;

class Brand implements \JsonSerializable
{
    private $brandID;
    private $brandName;

    public function __construct($brandID = 0)
    {
        if($brandID > 0) {
            $this->brandID = $brandID;
            $this->populate();
        }
    }
    private function setIDAndName($brandID, $brandName){
        $this->brandID = $brandID;
        $this->brandName = $brandName;
    }

    public function JsonSerialize()
    {
        $json = [
            'brandID' => $this->brandID,
            'brandName' => $this->brandName
        ];

        return $json;
    }
    public function populate()
    {
        $db = dbConnection::getInstance();
        //Build database query
        //Template
        $stmSelect = $db->prepare(
            'SELECT * FROM `Brand` 
            WHERE `brandID` = :brandID
            AND `isActive` = 1');

        //Bind
        $stmSelect->bindParam('brandID', $this->brandID);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        //Fetch results
        $results = $stmSelect->fetch();

        if ($results) {
            //If brand was found in the database, populate model
            $this->brandName = $results['brandName'];
        }
        else{
            //Brand wasn't found in the data base
            return -1;
        }

    }

    public function getAllBrands()
    {
        $db = dbConnection::getInstance();
        //Build database query
        //Template
        $stmSelect = $db->prepare(
            'SELECT * FROM `Brand` 
            WHERE `isActive` = 1');

        //Bind
        $stmSelect->bindParam('brandID', $this->brandID);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        $brandArray = array();
        //Fetch results
        while($results = $stmSelect->fetch()){
            $brand = new Brand();
            $brand->setIDAndName($results['brandID'], $results['brandName']);
            array_push($brandArray, $brand);
        }

        return $brandArray; //Add a check to see if it's empty or not

    }
}