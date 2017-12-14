<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/13/2017
 * Time: 5:07 PM
 */

namespace BowlingBall\Testing;

use BowlingBall\Controllers\TokensController;
use BowlingBall\Controllers\BowlingBallsController;
use BowlingBall\Http\StatusCodes;
use BowlingBall\Models\BowlingBall;
use BowlingBall\Http\Methods;
use BowlingBall\Utilities\Testing;
use \PHPUnit\Framework\TestCase;

class testBowlingBall extends TestCase
{
    //Test POST
    //No token
    //Commented out function causes an Apache error
    /*public function testPostNoToken(){
        $bowlingBallCtrl = new BowlingBallsController();
        $newBowlingBallData = array("bowlingBallName"=>"Unit Test BowlingBall");

        $this->assertNotEmpty($bowlingBallCtrl->createBowlingBall($newBowlingBallData), "No BowlingBalls Found");
    }*/
    public function testPostNoTokenCURL()
    {
        $token = "";
        $body_contents = array("bowlingBallName"=>"Unit Test BowlingBall", "brandName" => "Unit Test Brand",
            "coreTypeName" => "Unit Test Core", "coverstockTypeName" => "Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals('No token provided', $output);
        $this->assertEquals(StatusCodes::UNAUTHORIZED, Testing::getLastHTTPResponseCode());
    }

    //Dev token
    //Causes testing to stop
    /*public function testPostDevToken(){
        $token = $this->generateToken("genericDev","Dev");
        $bowlingBallCtrl = new BowlingBallsController();
        $bowlingBallData = array();
        $bowlingBallData['bowlingBallName'] = "Unit Test BowlingBall";
        $bowlingBallCtrl->createBowlingBall($bowlingBallData, $token);
    }*/
    public function testPostDevTokenCURL()
    {
        $token = $this->generateToken("genericDev", "Dev");
        $body_contents = array("bowlingBallName"=>"Unit Test BowlingBall", "brandName" => "Unit Test Brand",
            "coreTypeName" => "Unit Test Core", "coverstockTypeName" => "Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals('Improper Role', $output);
        $this->assertEquals(StatusCodes::FORBIDDEN, Testing::getLastHTTPResponseCode());
    }

    //Admin token
    public function testPostAdminToken(){
        $token = $this->generateToken("genericAdmin","Admin");
        $bowlingBallCtrl = new BowlingBallsController();
        $bowlingBallData = array();
        $bowlingBallData['bowlingBallName'] = "Unit Test BowlingBall";
        $bowlingBallData['brandName'] = "Unit Test Brand";
        $bowlingBallData['coreTypeName'] = "Unit Test Core";
        $bowlingBallData['coverstockTypeName'] = "Unit Test Coverstock";
        $output = $bowlingBallCtrl->createBowlingBall($bowlingBallData, $token);

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $output['bowlingBallID']);
        $this->assertEquals("Unit Test BowlingBall", $output['bowlingBallName']);

        $this->deleteTestBowlingBall();
    }
    public function testPostAdminTokenCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("bowlingBallName"=>"Unit Test BowlingBall", "brandName" => "Unit Test Brand",
            "coreTypeName" => "Unit Test Core", "coverstockTypeName" => "Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        $this->assertNotEmpty($output, $output);
        $this->assertEquals(StatusCodes::CREATED, Testing::getLastHTTPResponseCode());
    }
    public function testPostDuplicateDataCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("bowlingBallName"=>"Unit Test BowlingBall", "brandName" => "Unit Test Brand",
            "coreTypeName" => "Unit Test Core", "coverstockTypeName" => "Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
            $outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $outputJSON->bowlingBallID);
        $this->assertEquals(StatusCodes::CREATED, Testing::getLastHTTPResponseCode());
    }
    public function testPostWackyDataCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("bowlingBallName"=>"Unit Test BowlingBall%^^&$#% ", "brandName" => "Unit Test Brand",
            "coreTypeName" => "Unit Test Core", "coverstockTypeName" => "Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        $this->assertNotEmpty($output);
        $this->assertEquals(StatusCodes::CREATED, Testing::getLastHTTPResponseCode());
    }
    public function testPostEmptyCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("bowlingBallName"=>"", "brandName" => "",
            "coreTypeName" => "Unit Test Core", "coverstockTypeName" => "Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals("Invalid data in request body", $output);
        $this->assertEquals(StatusCodes::BAD_REQUEST, Testing::getLastHTTPResponseCode());
    }

    //Test GET
    //Test: Get All

    //Test: No token
    public function testGetAllNoTokenCURL(){
        $token = "";
        //$body_contents = array("username"=>"genericAdmin");
        //$body = json_encode($body_contents);
        $body = "";
        $endpoint = "/bowlingBalls";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "API Call failed";
        }

        $this->assertEquals(StatusCodes::UNAUTHORIZED, Testing::getLastHTTPResponseCode());
        $this->assertEquals("No token provided", $output);
    }
    //Test: Dev token
    public function testGetAllDevToken(){
        $token = $this->generateToken("genericDev", "Dev");
        $bowlingBallCtrl = new BowlingBallsController();

        $this->assertNotEmpty($bowlingBallCtrl->getAllBowlingBalls($token), "No BowlingBalls Found");
    }
    public function testGetAllDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/bowlingBalls";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No bowlingBalls in database";
        }

        $this->assertNotEquals("No bowlingBalls in database", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    //Test: Admin token
    public function testGetAllAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $bowlingBallCtrl = new BowlingBallsController();

        $this->assertNotEmpty($bowlingBallCtrl->getAllBowlingBalls($token), "No BowlingBalls Found");
    }
    public function testGetAllAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/bowlingBalls";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No bowlingBalls in database";
        }

        $this->assertNotEquals("No bowlingBalls in database", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    //Test: Get One
    //Test: No token
    public function testGetOneNoTokenCURL(){
        $token = "";
        //$body_contents = array("username"=>"genericAdmin");
        //$body = json_encode($body_contents);
        $body = "";
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "API Call failed";
        }

        $this->assertEquals(StatusCodes::UNAUTHORIZED, Testing::getLastHTTPResponseCode());
        $this->assertEquals("No token provided", $output);
    }
    //Test: Dev token
    public function testGetOneDevToken(){
        $token = $this->generateToken("genericDev", "Dev");
        $bowlingBallCtrl = new BowlingBallsController();

        $output = $bowlingBallCtrl->getBowlingBallByID(1, $token);

        $this->assertNotEquals("BowlingBall not found", $output, "BowlingBall not found");
        $this->assertNotEmpty($output, "BowlingBall not found");
    }
    public function testGetOneDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No bowlingBalls in database";
        }

        $this->assertNotEquals("BowlingBall not found", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }
    //Test: Admin token
    public function testGetOneAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $bowlingBallCtrl = new BowlingBallsController();

        $output = $bowlingBallCtrl->getBowlingBallByID(1, $token);

        $this->assertNotEquals("BowlingBall not found", $output, "BowlingBall not found");
        $this->assertNotEmpty($output, "BowlingBall not found");
    }
    public function testGetOneAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No bowlingBalls in database";
        }

        $this->assertNotEquals("BowlingBall not found", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }
    //Test: Invalid BowlingBall Number
    //Causes the testing to halt
    /*public function testGetOneInvalid(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $bowlingBallCtrl = new BowlingBallsController();

        $output = $bowlingBallCtrl->getBowlingBallByID(900000, $token);

        $this->assertNotEquals("BowlingBall not found", $output, "BowlingBall not found");
        $this->assertNotEmpty($output, "BowlingBall not found");
    }/**/

    public function testGetOneInvalidCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/bowlingBalls/900000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No bowlingBalls in database";
        }

        $this->assertEquals("BowlingBall not found", $output);
        $this->assertEquals(StatusCodes::NOT_FOUND, Testing::getLastHTTPResponseCode());
    }

    //Test PATCH
    //No token
    public function testPatchNoTokenCURL()
    {
        $token = "";
        $body_contents = array("bowlingBallName"=>"Unit Test BowlingBall Patch");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals('No token provided', $output);
        $this->assertEquals(StatusCodes::UNAUTHORIZED, Testing::getLastHTTPResponseCode());
    }

    //Dev token
    public function testPatchDevTokenCURL()
    {
        $token = $this->generateToken("genericDev", "Dev");
        $body_contents = array("bowlingBallName"=>"Unit Test BowlingBall Patch");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals('Improper Role', $output);
        $this->assertEquals(StatusCodes::FORBIDDEN, Testing::getLastHTTPResponseCode());
    }

    //Admin token
    public function testPatchAdminTokenCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("bowlingBallName"=>"Unit Test BowlingBall Patch");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
            $outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $outputJSON->bowlingBallID);
        $this->assertEquals("Unit Test BowlingBall Patch", $outputJSON->bowlingBallName);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());

        $this->revertFromPatchTests();
    }
    public function testPatchInvalidBowlingBallNameCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("bowlingBallName"=>"");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals("Invalid data in request body", $output);
        $this->assertEquals(StatusCodes::BAD_REQUEST, Testing::getLastHTTPResponseCode());
    }
    public function testPatchInvalidBowlingBallIDCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("bowlingBallName"=>"La de Dah");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls/9000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
            //$outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals("BowlingBall not found", $output);
        $this->assertEquals(StatusCodes::NOT_FOUND, Testing::getLastHTTPResponseCode());
    }

    //Test DELETE
    //Test: No token
    public function testDeleteNoTokenCURL(){
        $token = "";
        $body = "";
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "API Call failed";
        }

        $this->assertEquals(StatusCodes::UNAUTHORIZED, Testing::getLastHTTPResponseCode());
        $this->assertEquals("No token provided", $output);
    }
    //Test: Dev token
    //Causes testing to halt
    /*
    public function testDeleteDevToken(){
        $token = $this->generateToken("genericDev", "Dev");
        $bowlingBallCtrl = new BowlingBallsController();

        $output = $bowlingBallCtrl->deleteBowlingBall(1, $token);

        $this->assertEquals("Improper Role", $output, "Improper role failed to trigger");
    }*/
    public function testDeleteDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No bowlingBalls in database";
        }

        $this->assertEquals("Improper Role", $output);
        $this->assertEquals(StatusCodes::FORBIDDEN, Testing::getLastHTTPResponseCode());
    }
    //Test: Admin token
    public function testDeleteAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $bowlingBallCtrl = new BowlingBallsController();

        $output = $bowlingBallCtrl->deleteBowlingBall(1, $token);

        $this->assertEquals("Success", $output, "Delete failed");
        $this->restoreDeleted();
    }
    public function testDeleteAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No bowlingBalls in database";
        }

        $this->assertEquals('"Success"', $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    public function testDeleteInvalidAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $bowlingBallCtrl = new BowlingBallsController();

        $output = $bowlingBallCtrl->deleteBowlingBall(9000000, $token);

        $this->assertEquals("Success", $output, "Delete failed");
        $this->restoreDeleted();
    }
    public function testDeleteInvalidAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/bowlingBalls/9000000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No bowlingBalls in database";
        }

        $this->assertEquals('"Success"', $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    //Utility Functions
    private function generateToken($username, $password)
    {
        $tokenController = new TokensController();
        return $tokenController->buildToken($username, $password);
    }
    private function revertFromPatchTests(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("bowlingBallName"=>"Unit Test BowlingBall", "brandName" => "Unit Test Brand",
            "coreTypeName" => "Unit Test Core", "coverstockTypeName" => "Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/bowlingBalls/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }
    }
    private function restoreDeleted(){
        $bowlingBall = new BowlingBall();
        $bowlingBall->setBowlingBallName("Unit Test BowlingBall");
        $bowlingBall->createBowlingBall();
    }
    private function deleteTestBowlingBall(){
        $bowlingBall = new BowlingBall(1);
        $bowlingBall->deleteBowlingBall();
    }
}