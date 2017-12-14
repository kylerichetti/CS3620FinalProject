<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/10/2017
 * Time: 3:32 PM
 */

namespace BowlingBall\Models;

use \BowlingBall\Utilities\DatabaseConnection as dbConnection;

class Core implements \JsonSerializable
{
    private $coreTypeID;
    private $coreTypeName;

    public function __construct($coreTypeID = 0)
    {
        if($coreTypeID != 0) {
            $this->coreTypeID = $coreTypeID;
            $this->populate();
        }
    }
    public function getCoreTypeID(){
        return $this->coreTypeID;
    }
    public function setIDFromName(){
        $db = dbConnection::getInstance();

        $stmSelect = $db->prepare('SELECT * FROM `CoreType` WHERE `coreTypeName` LIKE :coreTypeName');

        $stmSelect->bindParam('coreTypeName', $this->coreTypeName);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        //Fetch results
        $results = $stmSelect->fetch();

        if ($results) {
            $this->coreTypeID = $results['coreTypeID'];
            return true;
        }
        else{
            return false;
        }
    }
    public function getCoreTypeName()
    {
        return $this->coreTypeName;
    }
    public function setCoreTypeName($name){
        //Sanitize data
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        if(!empty($name)) {
            $this->coreTypeName = $name;
        }
    }
    public function setAtr($atr, $value){
        switch($atr){
            case "coreTypeName":
                $this->setCoreTypeName($value);
                break;
        }
    }

    public function JsonSerialize()
    {
        $json = [
            'coreTypeID' => $this->coreTypeID,
            'coreTypeName' => $this->coreTypeName
        ];

        return $json;
    }
    public function populate()
    {
        $db = dbConnection::getInstance();
        //Build database query
        //Template
        $stmSelect = $db->prepare(
            'SELECT * FROM `CoreType` 
            WHERE `coreTypeID` = :coreTypeID
            AND `isActive` = 1');

        //Bind
        $stmSelect->bindParam('coreTypeID', $this->coreTypeID);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        //Fetch results
        $results = $stmSelect->fetch();

        if ($results) {
            //If core was found in the database, populate model
            $this->coreTypeName = $results['coreTypeName'];
        }
        else{
            //Core wasn't found in the data base
            $this->coreTypeName = NULL;
        }

    }

    public function getAllCores()
    {
        $db = dbConnection::getInstance();
        //Build database query
        //Template
        $stmSelect = $db->prepare(
            'SELECT * FROM `CoreType` 
            WHERE `isActive` = 1');

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        $coreArray = array();
        //Fetch results
        while($results = $stmSelect->fetch()){
            $core = new Core($results['coreTypeID']);
            array_push($coreArray, $core);
        }

        if(empty($coreArray)){
            return false;
        }

        return $coreArray; //Add a check to see if it's empty or not

    }
    public function createCore(){
        $nameExists = $this->checkDatabaseForCoreName(0);
        $db = dbConnection::getInstance();
        if($nameExists){
            //If core already exists, just make it active again
            $stmInsert = $db->prepare('UPDATE `CoreType` SET `isActive`=1 WHERE `coreTypeName` = :coreTypeName');
        }
        else {
            //If not, insert new core into database
            $stmInsert = $db->prepare('INSERT INTO `CoreType` (`coreTypeName`, `isActive`) VALUES (:coreTypeName, 1)');
        }
        //Bind parameters
        $stmInsert->bindParam(':coreTypeName', $this->coreTypeName);

        //Execute
        if($stmInsert->execute()){
            //Return true to indicate success
            $this->setIDFromName();
            return true;
        }
        //Something went wrong with the insert
        return false;
    }
    public function updateCore(){
        //Get DB connection
        $db = dbConnection::getInstance();

        //Prep update statement
        $updateStm = $db->prepare('UPDATE `CoreType` SET `coreTypeName`=:coreTypeName WHERE `coreTypeID` = :coreTypeID');

        //Bind params
        $updateStm->bindParam('coreTypeName', $this->coreTypeName);
        $updateStm->bindParam('coreTypeID', $this->coreTypeID);

        //Execute and return success or failure
        if($updateStm->execute()){
            return true;
        }

        return false;
    }
    public function deleteCore(){
        //Get DB connection
        $db = dbConnection::getInstance();

        //Prep update statement
        $deleteStm = $db->prepare('UPDATE `CoreType` SET `isActive`= 0 WHERE `coreTypeID` = :coreTypeID');

        //Bind params
        $deleteStm->bindParam('coreTypeID', $this->coreTypeID);

        //Execute
        //Return success or failure
        if($deleteStm->execute()){
            return true;
        }
        return false;
    }
    
    private function checkDatabaseForCoreName(bool $isActive){
        $db = dbConnection::getInstance();
        //Param decides if we only want cores that are active or all cores

        //Build database query
        //Template
        if($isActive) {
            $stmSelect = $db->prepare(
                'SELECT * FROM `CoreType` 
            WHERE `isActive` = 1
            AND `coreTypeName` = :coreTypeName');
        }
        else{
            $stmSelect = $db->prepare(
                'SELECT * FROM `CoreType` 
            WHERE `coreTypeName` = :coreTypeName');
        }

        //Bind
        //Core name has already been set by the controller
        $stmSelect->bindParam('coreTypeName', $this->coreTypeName);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();
        $results = $stmSelect->fetch();
        return $results;
    }
}