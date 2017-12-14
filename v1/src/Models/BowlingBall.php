<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/12/2017
 * Time: 3:40 PM
 */

namespace BowlingBall\Models;

use \BowlingBall\Utilities\DatabaseConnection as dbConnection;
use \BowlingBall\Models\Brand as Brand;
use \BowlingBall\Models\Core as Core;
use \BowlingBall\Models\Coverstock as Coverstock;

class BowlingBall implements \JsonSerializable
{
    private $bowlingBallID;
    private $bowlingBallName;
    private $bowlingBallBrand;
    private $bowlingBallCore;
    private $bowlingBallCoverstock;

    public function __construct($bowlingBallID = 0)
    {
        if($bowlingBallID != 0) {
            $this->bowlingBallID = $bowlingBallID;
            $this->populate();
        }
        else{
            $this->bowlingBallBrand = new Brand();
            $this->bowlingBallCore = new Core();
            $this->bowlingBallCoverstock = new Coverstock();
        }
    }
    public function getBowlingBallID(){
        return $this->bowlingBallID;
    }
    public function setIDFromName(){
        $db = dbConnection::getInstance();

        $stmSelect = $db->prepare('SELECT * FROM `BowlingBall` WHERE `bowlingBallName` LIKE :bowlingBallName');

        $stmSelect->bindParam('bowlingBallName', $this->bowlingBallName);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        //Fetch results
        $results = $stmSelect->fetch();

        if ($results) {
            $this->bowlingBallID = $results['bowlingBallID'];
            $this->populate();
        }
    }
    public function getBowlingBallName()
    {
        return $this->bowlingBallName;
    }
    public function setBowlingBallName($name){
        //Sanitize data
        if(!empty($name))
            $this->bowlingBallName = filter_var($name, FILTER_SANITIZE_STRING);
    }
    public function setBowlingBallBrand($brandName){
        $this->bowlingBallBrand = new Brand();
        $this->bowlingBallBrand->setBrandName($brandName);
        return $this->bowlingBallBrand->setIDFromName();
    }
    public function setBowlingBallCore($coreName){
        $this->bowlingBallCore = new Core();
        $this->bowlingBallCore->setCoreTypeName($coreName);
        return $this->bowlingBallCore->setIDFromName();
    }
    public function setBowlingBallCoverstock($coverstockName){
        $this->bowlingBallCoverstock = new Coverstock();
        $this->bowlingBallCoverstock->setCoverstockTypeName($coverstockName);
        return $this->bowlingBallCoverstock->setIDFromName();
    }
    public function setAtr($atr, $value){
        switch($atr){
            case "brandName":
                $this->setBowlingBallBrand($value);
                break;
            case "bowlingBallName":
                $this->setBowlingBallName($value);
                break;
            case "coreTypeName":
                $this->setBowlingBallCore($value);
                break;
            case "coverstockTypeName":
                $this->setBowlingBallCoverstock($value);
                break;
            default:
                break;
        }
    }

    public function JsonSerialize()
    {
        $json = [
            'bowlingBallID' => $this->bowlingBallID,
            'bowlingBallName' => $this->bowlingBallName,
            'bowlingBallBrand' => $this->bowlingBallBrand,
            'bowlingBallCore' => $this->bowlingBallCore,
            'bowlingBallCoverstock' => $this->bowlingBallCoverstock
        ];

        return $json;
    }
    public function populate()
    {
        $db = dbConnection::getInstance();
        //Build database query
        //Template
        $stmSelect = $db->prepare(
            'SELECT * FROM `BowlingBall` 
            WHERE `bowlingBallID` = :bowlingBallID
            AND `isActive` = 1');

        //Bind
        $stmSelect->bindParam('bowlingBallID', $this->bowlingBallID);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        //Fetch results
        $results = $stmSelect->fetch();

        if ($results) {
            //If bowlingBall was found in the database, populate model
            $this->bowlingBallName = $results['bowlingBallName'];
            $this->bowlingBallBrand = new Brand($results['brandID']);
            $this->bowlingBallCore = new Core($results['coreTypeID']);
            $this->bowlingBallCoverstock = new Coverstock($results['coverstockTypeID']);
        }
        else{
            //BowlingBall wasn't found in the data base
            $this->bowlingBallName = NULL;
        }

    }

    public function getAllBowlingBalls()
    {
        $db = dbConnection::getInstance();
        //Build database query
        //Template
        $stmSelect = $db->prepare(
            'SELECT * FROM `BowlingBall` 
            WHERE `isActive` = 1');

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();

        $bowlingBallArray = array();
        //Fetch results
        while($results = $stmSelect->fetch()){
            $bowlingBall = new BowlingBall($results['bowlingBallID']);
            array_push($bowlingBallArray, $bowlingBall);
        }

        if(empty($bowlingBallArray)){
            return false;
        }

        return $bowlingBallArray; //Add a check to see if it's empty or not

    }
    public function createBowlingBall(){
        $nameExists = $this->checkDatabaseForBowlingBallName(0);
        $db = dbConnection::getInstance();
        if($nameExists){
            //If bowlingBall already exists, just make it active again
            $stmInsert = $db->prepare('UPDATE `BowlingBall` SET `isActive`=1 WHERE `bowlingBallName` = :bowlingBallName');
        }
        else {
            //If not, insert new bowlingBall into database
            $stmInsert = $db->prepare('INSERT INTO `BowlingBall` (`bowlingBallName`, `brandID`, `coreTypeID`, `coverstockTypeID`, `isActive`) VALUES (:bowlingBallName, :brandID, :coreTypeID, :coverstockTypeID, 1)');

            //Bind parameters
            $brandID = $this->bowlingBallBrand->getBrandID();
            $coreTypeID = $this->bowlingBallCore->getCoreTypeID();
            $coverstockTypeID = $this->bowlingBallCoverstock->getCoverstockTypeID();

            $stmInsert->bindParam(':brandID', $brandID);
            $stmInsert->bindParam(':coreTypeID', $coreTypeID);
            $stmInsert->bindParam(':coverstockTypeID', $coverstockTypeID);
        }

        $stmInsert->bindParam(':bowlingBallName', $this->bowlingBallName);



        //Execute
        if($stmInsert->execute()){
            //Return true to indicate success
            $this->setIDFromName();
            $this->populate();
            return true;
        }
        //Something went wrong with the insert
        return false;
    }
    public function updateBowlingBall(){
        //Get DB connection
        $db = dbConnection::getInstance();

        //Prep update statement
        $updateStm = $db->prepare('UPDATE `BowlingBall` 
            SET `bowlingBallName`=:bowlingBallName, `brandID`=:brandID, `coreTypeID`=:coreTypeID, `coverstockTypeID`=:coverstockTypeID 
            WHERE `bowlingBallID` = :bowlingBallID');

        $brandID = $this->bowlingBallBrand->getBrandID();
        $coreTypeID = $this->bowlingBallCore->getCoreTypeID();
        $coverstockTypeID = $this->bowlingBallCoverstock->getCoverstockTypeID();
        //return $this->bowlingBallBrand;

        //Bind params
        $updateStm->bindParam('bowlingBallID', $this->bowlingBallID);
        $updateStm->bindParam('bowlingBallName', $this->bowlingBallName);
        $updateStm->bindParam('brandID', $brandID);
        $updateStm->bindParam('coreTypeID', $coreTypeID);
        $updateStm->bindParam('coverstockTypeID', $coverstockTypeID);

        //Execute and return success or failure
        if($updateStm->execute()){
            //$this->setBowlingBallName($newBowlingBallName);
            //$this->populate();
            return true;
        }

        return false;
    }
    public function deleteBowlingBall(){
        //Get DB connection
        $db = dbConnection::getInstance();

        //Prep update statement
        $deleteStm = $db->prepare('UPDATE `BowlingBall` SET `isActive`= 0 WHERE `bowlingBallID` = :bowlingBallID');

        //Bind params
        $deleteStm->bindParam('bowlingBallID', $this->bowlingBallID);

        //Execute
        //Return success or failure
        if($deleteStm->execute()){
            return true;
        }
        return false;
    }

    private function checkDatabaseForBowlingBallName(bool $isActive){
        $db = dbConnection::getInstance();
        //Param decides if we only want bowlingBalls that are active or all bowlingBalls

        //Build database query
        //Template
        if($isActive) {
            $stmSelect = $db->prepare(
                'SELECT * FROM `BowlingBall` 
            WHERE `isActive` = 1
            AND `bowlingBallName` = :bowlingBallName');
        }
        else{
            $stmSelect = $db->prepare(
                'SELECT * FROM `BowlingBall` 
            WHERE `bowlingBallName` = :bowlingBallName');
        }

        //Bind
        //BowlingBall name has already been set by the controller
        $stmSelect->bindParam('bowlingBallName', $this->bowlingBallName);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        //Execute
        $stmSelect->execute();
        $results = $stmSelect->fetch();
        return $results;
    }
}