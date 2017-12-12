<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/11/2017
 * Time: 8:17 PM
 */

namespace BowlingBall\Models;


use \BowlingBall\Utilities\DatabaseConnection as dbConnection;

class User
{
    private $username;
    private $password;
    private $role;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->role = 'Invalid';
    }

    public function authenticate(){
        $message = [
            'userExists' => false,
            'correctPassword' => false
        ];

        if($this->testUserExists()){
            $message['userExists'] = true;
        }

        if($this->testPassword()){
            $message['correctPassword'] = true;
        }

        if($message['userExists'] && $message['correctPassword']){
            $this->getRoleFromDatabase();
        }

        return $message;
    }
    public function getUsername(){
        return $this->username;
    }
    public function getRole(){
        return $this->role;
    }

    private function testUserExists(){
        $db = dbConnection::getInstance();

        $stmSelect = $db->prepare('SELECT * FROM `User` WHERE `username` = :username');
        $stmSelect->bindParam('username', $this->username);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        $stmSelect->execute();

        if($stmSelect->fetch()){
            return true;
        }

        return false;
    }
    private function testPassword(){
        $db = dbConnection::getInstance();

        $stmSelect = $db->prepare('SELECT * FROM `User` WHERE `username` LIKE :username AND `password` LIKE :password');
        $stmSelect->bindParam('username', $this->username);
        $stmSelect->bindParam('password', $this->password);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        $stmSelect->execute();

        if($stmSelect->fetch()){
            return true;
        }

        return false;
    }
    private function getRoleFromDatabase(){
        $db = dbConnection::getInstance();

        $stmSelect = $db->prepare('SELECT * FROM `User` JOIN `Role` ON `User`.`roleID` = `Role`.`roleID` WHERE `username` LIKE :username AND `password` LIKE :password');
        $stmSelect->bindParam('username', $this->username);
        $stmSelect->bindParam('password', $this->password);

        $stmSelect->setFetchMode(\PDO::FETCH_ASSOC);

        $stmSelect->execute();

        $results = $stmSelect->fetch();

        if($results){
            $this->role = $results['roleName'];
        }
    }
}