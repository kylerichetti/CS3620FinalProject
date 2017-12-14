<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/10/2017
 * Time: 3:32 PM
 */

namespace BowlingBall\Models;

use \BowlingBall\Utilities\DatabaseConnection as dbConnection;

class Coverstock implements \JsonSerializable
{
    private $coverstockTypeID;
    private $coverstockTypeName;

    public function __construct($coverstockTypeID = 0)
    {
        if($coverstockTypeID != 0) {
            $this->coverstockTypeID = $coverstockTypeID;
            $this->populate();
        }
    }
    public function getCoverstockTypeID(){
        return $this->coverstockTypeID;
    }
    public function setIDFromName(){
        $db = dbConnection::getInstance();

        $stmSelect = $db->prepare('SELECT * FROM `CoverstockType` WHERE `coverstockTypeName` LIKE :coverstockTypeName');

        $stmSelect->bindParam('coverstockTypeName', $this->coverstockTypeName);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        //Fetch results
        $results = $stmSelect->fetch();

        if ($results) {
            $this->coverstockTypeID = $results['coverstockTypeID'];
            return true;
        }
        else{
            return false;
        }
    }
    public function getCoverstockTypeName()
    {
        return $this->coverstockTypeName;
    }
    public function setCoverstockTypeName($name){
        //Sanitize data
        if(!empty($name)) {
            $this->coverstockTypeName = filter_var($name, FILTER_SANITIZE_STRING);;
        }
    }
    public function setAtr($atr, $value){
        switch($atr){
            case "coverstockTypeName":
                $this->setCoverstockTypeName($value);
                break;
        }
    }

    public function JsonSerialize()
    {
        $json = [
            'coverstockTypeID' => $this->coverstockTypeID,
            'coverstockTypeName' => $this->coverstockTypeName
        ];

        return $json;
    }
    public function populate()
    {
        $db = dbConnection::getInstance();
        //Build database query
        //Template
        $stmSelect = $db->prepare(
            'SELECT * FROM `CoverstockType` 
            WHERE `coverstockTypeID` = :coverstockTypeID
            AND `isActive` = 1');

        //Bind
        $stmSelect->bindParam('coverstockTypeID', $this->coverstockTypeID);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        //Fetch results
        $results = $stmSelect->fetch();

        if ($results) {
            //If coverstock was found in the database, populate model
            $this->coverstockTypeName = $results['coverstockTypeName'];
        }
        else{
            //Coverstock wasn't found in the data base
            $this->coverstockTypeName = NULL;
        }

    }

    public function getAllCoverstocks()
    {
        $db = dbConnection::getInstance();
        //Build database query
        //Template
        $stmSelect = $db->prepare(
            'SELECT * FROM `CoverstockType` 
            WHERE `isActive` = 1');

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        $coverstockArray = array();
        //Fetch results
        while($results = $stmSelect->fetch()){
            $coverstock = new Coverstock($results['coverstockTypeID']);
            array_push($coverstockArray, $coverstock);
        }

        if(empty($coverstockArray)){
            return false;
        }

        return $coverstockArray; //Add a check to see if it's empty or not

    }
    public function createCoverstock(){
        $nameExists = $this->checkDatabaseForCoverstockName(0);
        $db = dbConnection::getInstance();
        if($nameExists){
            //If coverstock already exists, just make it active again
            $stmInsert = $db->prepare('UPDATE `CoverstockType` SET `isActive`=1 WHERE `coverstockTypeName` = :coverstockTypeName');
        }
        else {
            //If not, insert new coverstock into database
            $stmInsert = $db->prepare('INSERT INTO `CoverstockType` (`coverstockTypeName`, `isActive`) VALUES (:coverstockTypeName, 1)');
        }
        //Bind parameters
        $stmInsert->bindParam(':coverstockTypeName', $this->coverstockTypeName);

        //Execute
        if($stmInsert->execute()){
            //Return true to indicate success
            $this->setIDFromName();
            return true;
        }
        //Something went wrong with the insert
        return false;
    }
    public function updateCoverstock(){
        //Get DB connection
        $db = dbConnection::getInstance();

        //Prep update statement
        $updateStm = $db->prepare('UPDATE `CoverstockType` SET `coverstockTypeName`=:coverstockTypeName WHERE `coverstockTypeID` = :coverstockTypeID');

        //Bind params
        $updateStm->bindParam('coverstockTypeName', $this->coverstockTypeName);
        $updateStm->bindParam('coverstockTypeID', $this->coverstockTypeID);

        //Execute and return success or failure
        if($updateStm->execute()){
            return true;
        }

        return false;
    }
    public function deleteCoverstock(){
        //Get DB connection
        $db = dbConnection::getInstance();

        //Prep update statement
        $deleteStm = $db->prepare('UPDATE `CoverstockType` SET `isActive`= 0 WHERE `coverstockTypeID` = :coverstockTypeID');

        //Bind params
        $deleteStm->bindParam('coverstockTypeID', $this->coverstockTypeID);

        //Execute
        //Return success or failure
        if($deleteStm->execute()){
            return true;
        }
        return false;
    }

    private function checkDatabaseForCoverstockName(bool $isActive){
        $db = dbConnection::getInstance();
        //Param decides if we only want coverstocks that are active or all coverstocks

        //Build database query
        //Template
        if($isActive) {
            $stmSelect = $db->prepare(
                'SELECT * FROM `CoverstockType` 
            WHERE `isActive` = 1
            AND `coverstockTypeName` = :coverstockTypeName');
        }
        else{
            $stmSelect = $db->prepare(
                'SELECT * FROM `CoverstockType` 
            WHERE `coverstockTypeName` = :coverstockTypeName');
        }

        //Bind
        //Coverstock name has already been set by the controller
        $stmSelect->bindParam('coverstockTypeName', $this->coverstockTypeName);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();
        $results = $stmSelect->fetch();
        return $results;
    }
}