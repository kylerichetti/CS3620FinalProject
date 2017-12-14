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
        if($brandID != 0) {
            $this->brandID = $brandID;
            $this->populate();
        }
    }
    public function getBrandID(){
        return $this->brandID;
    }
    public function setIDFromName(){
        $db = dbConnection::getInstance();

        $stmSelect = $db->prepare('SELECT * FROM `Brand` WHERE `brandName` LIKE :brandName');

        $stmSelect->bindParam('brandName', $this->brandName);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        //Fetch results
        $results = $stmSelect->fetch();

        if ($results) {
            $this->brandID = $results['brandID'];
            return true;
        }
        else{
            return false;
        }
    }
    public function getBrandName()
    {
        return $this->brandName;
    }
    public function setBrandName($name){
        //Sanitize data
        //$this->brandName = filter_var($name, FILTER_SANITIZE_STRING);
        if(!empty($name))
            $this->brandName = $name;
    }
    public function setAtr($atr, $value){
        switch($atr){
            case "brandName":
                $this->setBrandName($value);
                break;
        }
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
            $this->brandName = NULL;
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

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        $brandArray = array();
        //Fetch results
        while($results = $stmSelect->fetch()){
            $brand = new Brand($results['brandID']);
            array_push($brandArray, $brand);
        }

        if(empty($brandArray)){
            return false;
        }

        return $brandArray; //Add a check to see if it's empty or not

    }
    public function createBrand(){
        $nameExists = $this->checkDatabaseForBrandName(0);
        $db = dbConnection::getInstance();
        if($nameExists){
            //If brand already exists, just make it active again
            $stmInsert = $db->prepare('UPDATE `Brand` SET `isActive`=1 WHERE `brandName` = :brandName');
        }
        else {
            //If not, insert new brand into database
            $stmInsert = $db->prepare('INSERT INTO `Brand` (`brandName`, `isActive`) VALUES (:brandName, 1)');
        }
        //Bind parameters
        $stmInsert->bindParam(':brandName', $this->brandName);

        //Execute
        if($stmInsert->execute()){
            //Return true to indicate success
            $this->setIDFromName();
            return true;
        }
        //Something went wrong with the insert
        return false;
    }
    public function updateBrand(){
        //Get DB connection
        $db = dbConnection::getInstance();

        //Prep update statement
        $updateStm = $db->prepare('UPDATE `Brand` SET `brandName`=:brandName WHERE `brandID` = :brandID');

        //Bind params
        $updateStm->bindParam('brandName', $this->brandName);
        $updateStm->bindParam('brandID', $this->brandID);

        //Execute and return success or failure
        if($updateStm->execute()){
            return true;
        }

        return false;
    }
    public function deleteBrand(){
        //Get DB connection
        $db = dbConnection::getInstance();

        //Prep update statement
        $deleteStm = $db->prepare('UPDATE `Brand` SET `isActive`= 0 WHERE `brandID` = :brandID');

        //Bind params
        $deleteStm->bindParam('brandID', $this->brandID);

        //Execute
        //Return success or failure
        if($deleteStm->execute()){
            return true;
        }
        return false;
    }

    private function checkDatabaseForBrandName(bool $isActive){
        $db = dbConnection::getInstance();
        //Param decides if we only want brands that are active or all brands

        //Build database query
        //Template
        if($isActive) {
            $stmSelect = $db->prepare(
                'SELECT * FROM `Brand` 
            WHERE `isActive` = 1
            AND `brandName` = :brandName');
        }
        else{
            $stmSelect = $db->prepare(
                'SELECT * FROM `Brand` 
            WHERE `brandName` = :brandName');
        }

        //Bind
        //Brand name has already been set by the controller
        $stmSelect->bindParam('brandName', $this->brandName);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();
        $results = $stmSelect->fetch();
        return $results;
    }
}