<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/13/2017
 * Time: 5:06 PM
 */

namespace BowlingBall\Testing;

use BowlingBall\Controllers\TokensController;
use BowlingBall\Controllers\CoresController;
use BowlingBall\Models\Core;
use BowlingBall\Http\StatusCodes;
use BowlingBall\Http\Methods;
use BowlingBall\Utilities\Testing;
use \PHPUnit\Framework\TestCase;

class testCore extends TestCase
{
    //Test POST
    //No token
    //Commented out function causes an Apache error
    /*public function testPostNoToken(){
        $coreCtrl = new CoresController();
        $newCoreData = array("coreTypeName"=>"Unit Test Core");

        $this->assertNotEmpty($coreCtrl->createCore($newCoreData), "No Cores Found");
    }*/
    public function testPostNoTokenCURL()
    {
        $token = "";
        $body_contents = array("coreTypeName"=>"Unit Test Core");
        $body = json_encode($body_contents);
        $endpoint = "/cores";

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
        $coreCtrl = new CoresController();
        $coreData = array();
        $coreData['coreTypeName'] = "Unit Test Core";
        $coreCtrl->createCore($coreData, $token);
    }*/
    public function testPostDevTokenCURL()
    {
        $token = $this->generateToken("genericDev", "Dev");
        $body_contents = array("coreTypeName"=>"Unit Test Core");
        $body = json_encode($body_contents);
        $endpoint = "/cores";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals('Improper Role', $output);
        $this->assertEquals(StatusCodes::FORBIDDEN, Testing::getLastHTTPResponseCode());
    }

    //Admin token
    public function testPostDevToken(){
        $token = $this->generateToken("genericAdmin","Admin");
        $coreCtrl = new CoresController();
        $coreData = array();
        $coreData['coreTypeName'] = "Unit Test Core";
        $output = $coreCtrl->createCore($coreData, $token);

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $output['coreTypeID']);
        $this->assertEquals("Unit Test Core", $output['coreTypeName']);

        $this->deleteTestCore();
    }
    public function testPostAdminTokenCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("coreTypeName"=>"Unit Test Core");
        $body = json_encode($body_contents);
        $endpoint = "/cores";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        $this->assertNotEmpty($output);
        $this->assertEquals(StatusCodes::CREATED, Testing::getLastHTTPResponseCode());
    }
    public function testPostDuplicateDataCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("coreTypeName"=>"Unit Test Core");
        $body = json_encode($body_contents);
        $endpoint = "/cores";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
            $outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $outputJSON->coreTypeID);
        $this->assertEquals(StatusCodes::CREATED, Testing::getLastHTTPResponseCode());
    }
    public function testPostWackyDataCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("coreTypeName"=>";Unit Test Core%^^&$#% ");
        $body = json_encode($body_contents);
        $endpoint = "/cores";

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
        $body_contents = array("coreTypeName"=>"");
        $body = json_encode($body_contents);
        $endpoint = "/cores";

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
        $endpoint = "/cores";

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
        $coreCtrl = new CoresController();

        $this->assertNotEmpty($coreCtrl->getAllCores($token), "No Cores Found");
    }
    public function testGetAllDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/cores";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No cores in database";
        }

        $this->assertNotEquals("No cores in database", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    //Test: Admin token
    public function testGetAllAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coreCtrl = new CoresController();

        $this->assertNotEmpty($coreCtrl->getAllCores($token), "No Cores Found");
    }
    public function testGetAllAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/cores";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No cores in database";
        }

        $this->assertNotEquals("No cores in database", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    //Test: Get One
    //Test: No token
    public function testGetOneNoTokenCURL(){
        $token = "";
        //$body_contents = array("username"=>"genericAdmin");
        //$body = json_encode($body_contents);
        $body = "";
        $endpoint = "/cores/1";

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
        $coreCtrl = new CoresController();

        $output = $coreCtrl->getCoreByID(1, $token);

        $this->assertNotEquals("Core not found", $output, "Core not found");
        $this->assertNotEmpty($output, "Core not found");
    }
    public function testGetOneDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/cores/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No cores in database";
        }

        $this->assertNotEquals("Core not found", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }
    //Test: Admin token
    public function testGetOneAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coreCtrl = new CoresController();

        $output = $coreCtrl->getCoreByID(1, $token);

        $this->assertNotEquals("Core not found", $output, "Core not found");
        $this->assertNotEmpty($output, "Core not found");
    }
    public function testGetOneAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/cores/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No cores in database";
        }

        $this->assertNotEquals("Core not found", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }
    //Test: Invalid Core Number
    //Causes the testing to halt
    /*public function testGetOneInvalid(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coreCtrl = new CoresController();

        $output = $coreCtrl->getCoreByID(900000, $token);

        $this->assertNotEquals("Core not found", $output, "Core not found");
        $this->assertNotEmpty($output, "Core not found");
    }/**/

    public function testGetOneInvalidCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/cores/900000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No cores in database";
        }

        $this->assertEquals("Core not found", $output);
        $this->assertEquals(StatusCodes::NOT_FOUND, Testing::getLastHTTPResponseCode());
    }

    //Test PATCH
    //No token
    public function testPatchNoTokenCURL()
    {
        $token = "";
        $body_contents = array("coreTypeName"=>"Unit Test Core Patch");
        $body = json_encode($body_contents);
        $endpoint = "/cores/1";

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
        $body_contents = array("coreTypeName"=>"Unit Test Core Patch");
        $body = json_encode($body_contents);
        $endpoint = "/cores/1";

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
        $body_contents = array("coreTypeName"=>"Unit Test Core Patch");
        $body = json_encode($body_contents);
        $endpoint = "/cores/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
            $outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        //$this->assertEquals(1, $outputJSON->coreTypeID, $output);
        $this->assertEquals("Unit Test Core Patch", $outputJSON->coreTypeName);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());

        $this->revertFromPatchTests();
    }
    public function testPatchInvalidCoreNameCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("coreTypeName"=>"");
        $body = json_encode($body_contents);
        $endpoint = "/cores/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals("Invalid data in request body", $output);
        $this->assertEquals(StatusCodes::BAD_REQUEST, Testing::getLastHTTPResponseCode());
    }
    public function testPatchInvalidCoreIDCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("coreTypeName"=>"La de Dah");
        $body = json_encode($body_contents);
        $endpoint = "/cores/9000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
            //$outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals("Core not found", $output);
        $this->assertEquals(StatusCodes::NOT_FOUND, Testing::getLastHTTPResponseCode());
    }


    //Test DELETE
    //Test: No token
    public function testDeleteNoTokenCURL(){
        $token = "";
        $body = "";
        $endpoint = "/cores/1";

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
        $coreCtrl = new CoresController();

        $output = $coreCtrl->deleteCore(1, $token);

        $this->assertEquals("Improper Role", $output, "Improper role failed to trigger");
    }*/
    public function testDeleteDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/cores/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No cores in database";
        }

        $this->assertEquals("Improper Role", $output);
        $this->assertEquals(StatusCodes::FORBIDDEN, Testing::getLastHTTPResponseCode());
    }
    //Test: Admin token
    public function testDeleteAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coreCtrl = new CoresController();

        $output = $coreCtrl->deleteCore(1, $token);

        $this->assertEquals("Success", $output, "Delete failed");
        $this->restoreDeleted();
    }
    public function testDeleteAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/cores/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No cores in database";
        }

        $this->assertEquals('"Success"', $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    public function testDeleteInvalidAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coreCtrl = new CoresController();

        $output = $coreCtrl->deleteCore(9000000, $token);

        $this->assertEquals("Success", $output, "Delete failed");
        $this->restoreDeleted();
    }
    public function testDeleteInvalidAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/cores/9000000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No cores in database";
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
        $body_contents = array("coreTypeName"=>"Unit Test Core");
        $body = json_encode($body_contents);
        $endpoint = "/cores/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }
    }
    private function restoreDeleted(){
        $core = new Core();
        $core->setCoreTypeName("Unit Test Core");
        $core->createCore();
    }
    private function deleteTestCore(){
        $core = new Core(1);
        $core->deleteCore();
    }
}