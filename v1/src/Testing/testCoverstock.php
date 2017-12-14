<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/13/2017
 * Time: 5:07 PM
 */

namespace BowlingBall\Testing;

use BowlingBall\Controllers\TokensController;
use BowlingBall\Controllers\CoverstocksController;
use BowlingBall\Models\Coverstock;
use BowlingBall\Http\StatusCodes;
use BowlingBall\Http\Methods;
use BowlingBall\Utilities\Testing;
use \PHPUnit\Framework\TestCase;

class testCoverstock extends TestCase
{
    //Test POST
    //No token
    //Commented out function causes an Apache error
    /*public function testPostNoToken(){
        $coverstockCtrl = new CoverstocksController();
        $newCoverstockData = array("coverstockTypeName"=>"Unit Test Coverstock");

        $this->assertNotEmpty($coverstockCtrl->createCoverstock($newCoverstockData), "No Coverstocks Found");
    }*/
    public function testPostNoTokenCURL()
    {
        $token = "";
        $body_contents = array("coverstockTypeName"=>"Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks";

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
        $coverstockCtrl = new CoverstocksController();
        $coverstockData = array();
        $coverstockData['coverstockTypeName'] = "Unit Test Coverstock";
        $coverstockCtrl->createCoverstock($coverstockData, $token);
    }*/
    public function testPostDevTokenCURL()
    {
        $token = $this->generateToken("genericDev", "Dev");
        $body_contents = array("coverstockTypeName"=>"Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks";

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
        $coverstockCtrl = new CoverstocksController();
        $coverstockData = array();
        $coverstockData['coverstockTypeName'] = "Unit Test Coverstock";
        $output = $coverstockCtrl->createCoverstock($coverstockData, $token);

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $output['coverstockTypeID']);
        $this->assertEquals("Unit Test Coverstock", $output['coverstockTypeName']);

        $this->deleteTestCoverstock();
    }
    public function testPostAdminTokenCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("coverstockTypeName"=>"Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks";

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
        $body_contents = array("coverstockTypeName"=>"Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
            $outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $outputJSON->coverstockTypeID);
        $this->assertEquals(StatusCodes::CREATED, Testing::getLastHTTPResponseCode());
    }
    public function testPostWackyDataCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("coverstockTypeName"=>";Unit Test Coverstock%^^&$#% ");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks";

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
        $body_contents = array("coverstockTypeName"=>"");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks";

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
        $endpoint = "/coverstocks";

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
        $coverstockCtrl = new CoverstocksController();

        $this->assertNotEmpty($coverstockCtrl->getAllCoverstocks($token), "No Coverstocks Found");
    }
    public function testGetAllDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/coverstocks";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No coverstocks in database";
        }

        $this->assertNotEquals("No coverstocks in database", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    //Test: Admin token
    public function testGetAllAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coverstockCtrl = new CoverstocksController();

        $this->assertNotEmpty($coverstockCtrl->getAllCoverstocks($token), "No Coverstocks Found");
    }
    public function testGetAllAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/coverstocks";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No coverstocks in database";
        }

        $this->assertNotEquals("No coverstocks in database", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    //Test: Get One
    //Test: No token
    public function testGetOneNoTokenCURL(){
        $token = "";
        //$body_contents = array("username"=>"genericAdmin");
        //$body = json_encode($body_contents);
        $body = "";
        $endpoint = "/coverstocks/1";

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
        $coverstockCtrl = new CoverstocksController();

        $output = $coverstockCtrl->getCoverstockByID(1, $token);

        $this->assertNotEquals("Coverstock not found", $output, "Coverstock not found");
        $this->assertNotEmpty($output, "Coverstock not found");
    }
    public function testGetOneDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/coverstocks/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No coverstocks in database";
        }

        $this->assertNotEquals("Coverstock not found", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }
    //Test: Admin token
    public function testGetOneAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coverstockCtrl = new CoverstocksController();

        $output = $coverstockCtrl->getCoverstockByID(1, $token);

        $this->assertNotEquals("Coverstock not found", $output, "Coverstock not found");
        $this->assertNotEmpty($output, "Coverstock not found");
    }
    public function testGetOneAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/coverstocks/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No coverstocks in database";
        }

        $this->assertNotEquals("Coverstock not found", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }
    //Test: Invalid Coverstock Number
    //Causes the testing to halt
    /*public function testGetOneInvalid(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coverstockCtrl = new CoverstocksController();

        $output = $coverstockCtrl->getCoverstockByID(900000, $token);

        $this->assertNotEquals("Coverstock not found", $output, "Coverstock not found");
        $this->assertNotEmpty($output, "Coverstock not found");
    }/**/

    public function testGetOneInvalidCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/coverstocks/900000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No coverstocks in database";
        }

        $this->assertEquals("Coverstock not found", $output);
        $this->assertEquals(StatusCodes::NOT_FOUND, Testing::getLastHTTPResponseCode());
    }

    //Test PATCH
    //No token
    public function testPatchNoTokenCURL()
    {
        $token = "";
        $body_contents = array("coverstockTypeName"=>"Unit Test Coverstock Patch");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks/1";

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
        $body_contents = array("coverstockTypeName"=>"Unit Test Coverstock Patch");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks/1";

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
        $body_contents = array("coverstockTypeName"=>"Unit Test Coverstock Patch");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
            $outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        //$this->assertEquals(1, $outputJSON->coverstockTypeID, $output);
        $this->assertEquals("Unit Test Coverstock Patch", $outputJSON->coverstockTypeName);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());

        $this->revertFromPatchTests();
    }
    public function testPatchInvalidCoverstockNameCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("coverstockTypeName"=>"");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals("Invalid data in request body", $output);
        $this->assertEquals(StatusCodes::BAD_REQUEST, Testing::getLastHTTPResponseCode());
    }
    public function testPatchInvalidCoverstockIDCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("coverstockTypeName"=>"La de Dah");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks/9000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
            //$outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals("Coverstock not found", $output);
        $this->assertEquals(StatusCodes::NOT_FOUND, Testing::getLastHTTPResponseCode());
    }


    //Test DELETE
    //Test: No token
    public function testDeleteNoTokenCURL(){
        $token = "";
        $body = "";
        $endpoint = "/coverstocks/1";

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
        $coverstockCtrl = new CoverstocksController();

        $output = $coverstockCtrl->deleteCoverstock(1, $token);

        $this->assertEquals("Improper Role", $output, "Improper role failed to trigger");
    }*/
    public function testDeleteDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/coverstocks/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No coverstocks in database";
        }

        $this->assertEquals("Improper Role", $output);
        $this->assertEquals(StatusCodes::FORBIDDEN, Testing::getLastHTTPResponseCode());
    }
    //Test: Admin token
    public function testDeleteAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coverstockCtrl = new CoverstocksController();

        $output = $coverstockCtrl->deleteCoverstock(1, $token);

        $this->assertEquals("Success", $output, "Delete failed");
        $this->restoreDeleted();
    }
    public function testDeleteAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/coverstocks/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No coverstocks in database";
        }

        $this->assertEquals('"Success"', $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    public function testDeleteInvalidAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $coverstockCtrl = new CoverstocksController();

        $output = $coverstockCtrl->deleteCoverstock(9000000, $token);

        $this->assertEquals("Success", $output, "Delete failed");
        $this->restoreDeleted();
    }
    public function testDeleteInvalidAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/coverstocks/9000000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No coverstocks in database";
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
        $body_contents = array("coverstockTypeName"=>"Unit Test Coverstock");
        $body = json_encode($body_contents);
        $endpoint = "/coverstocks/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }
    }
    private function restoreDeleted(){
        $coverstock = new Coverstock();
        $coverstock->setCoverstockTypeName("Unit Test Coverstock");
        $coverstock->createCoverstock();
    }
    private function deleteTestCoverstock(){
        $coverstock = new Coverstock(1);
        $coverstock->deleteCoverstock();
    }
}