<?php
/**
 * Created by PhpStorm.
 * User: kyler_000
 * Date: 12/13/2017
 * Time: 3:46 PM
 */

namespace BowlingBall\Testing;

use BowlingBall\Controllers\TokensController;
use BowlingBall\Controllers\BrandsController;
use BowlingBall\Http\StatusCodes;
use BowlingBall\Models\Brand;
use BowlingBall\Http\Methods;
use BowlingBall\Utilities\Testing;
use \PHPUnit\Framework\TestCase;

class testBrand extends TestCase
{
    //Test POST
    //No token
    //Commented out function causes an Apache error
    /*public function testPostNoToken(){
        $brandCtrl = new BrandsController();
        $newBrandData = array("brandName"=>"Unit Test Brand");

        $this->assertNotEmpty($brandCtrl->createBrand($newBrandData), "No Brands Found");
    }*/
    public function testPostNoTokenCURL()
    {
        $token = "";
        $body_contents = array("brandName"=>"Unit Test Brand");
        $body = json_encode($body_contents);
        $endpoint = "/brands";

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
        $brandCtrl = new BrandsController();
        $brandData = array();
        $brandData['brandName'] = "Unit Test Brand";
        $brandCtrl->createBrand($brandData, $token);
    }*/
    public function testPostDevTokenCURL()
    {
        $token = $this->generateToken("genericDev", "Dev");
        $body_contents = array("brandName"=>"Unit Test Brand");
        $body = json_encode($body_contents);
        $endpoint = "/brands";

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
        $brandCtrl = new BrandsController();
        $brandData = array();
        $brandData['brandName'] = "Unit Test Brand";
        $output = $brandCtrl->createBrand($brandData, $token);

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $output['brandID']);
        $this->assertEquals("Unit Test Brand", $output['brandName']);

        $this->deleteTestBrand();
    }
    public function testPostAdminTokenCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("brandName"=>"Unit Test Brand");
        $body = json_encode($body_contents);
        $endpoint = "/brands";

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
        $body_contents = array("brandName"=>"Unit Test Brand");
        $body = json_encode($body_contents);
        $endpoint = "/brands";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
            $outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $outputJSON->brandID);
        $this->assertEquals(StatusCodes::CREATED, Testing::getLastHTTPResponseCode());
    }
    public function testPostWackyDataCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("brandName"=>";Unit Test Brand%^^&$#% ");
        $body = json_encode($body_contents);
        $endpoint = "/brands";

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
        $body_contents = array("brandName"=>"");
        $body = json_encode($body_contents);
        $endpoint = "/brands";

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
        $endpoint = "/brands";

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
        $brandCtrl = new BrandsController();

        $this->assertNotEmpty($brandCtrl->getAllBrands($token), "No Brands Found");
    }
    public function testGetAllDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/brands";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No brands in database";
        }

        $this->assertNotEquals("No brands in database", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    //Test: Admin token
    public function testGetAllAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $brandCtrl = new BrandsController();

        $this->assertNotEmpty($brandCtrl->getAllBrands($token), "No Brands Found");
    }
    public function testGetAllAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/brands";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No brands in database";
        }

        $this->assertNotEquals("No brands in database", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    //Test: Get One
    //Test: No token
    public function testGetOneNoTokenCURL(){
        $token = "";
        //$body_contents = array("username"=>"genericAdmin");
        //$body = json_encode($body_contents);
        $body = "";
        $endpoint = "/brands/1";

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
        $brandCtrl = new BrandsController();

        $output = $brandCtrl->getBrandByID(1, $token);

        $this->assertNotEquals("Brand not found", $output, "Brand not found");
        $this->assertNotEmpty($output, "Brand not found");
    }
    public function testGetOneDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/brands/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No brands in database";
        }

        $this->assertNotEquals("Brand not found", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }
    //Test: Admin token
    public function testGetOneAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $brandCtrl = new BrandsController();

        $output = $brandCtrl->getBrandByID(1, $token);

        $this->assertNotEquals("Brand not found", $output, "Brand not found");
        $this->assertNotEmpty($output, "Brand not found");
    }
    public function testGetOneAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/brands/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No brands in database";
        }

        $this->assertNotEquals("Brand not found", $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }
    //Test: Invalid Brand Number
    //Causes the testing to halt
    /*public function testGetOneInvalid(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $brandCtrl = new BrandsController();

        $output = $brandCtrl->getBrandByID(900000, $token);

        $this->assertNotEquals("Brand not found", $output, "Brand not found");
        $this->assertNotEmpty($output, "Brand not found");
    }/**/

    public function testGetOneInvalidCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/brands/900000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::GET, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No brands in database";
        }

        $this->assertEquals("Brand not found", $output);
        $this->assertEquals(StatusCodes::NOT_FOUND, Testing::getLastHTTPResponseCode());
    }

    //Test PATCH
    //No token
    public function testPatchNoTokenCURL()
    {
        $token = "";
        $body_contents = array("brandName"=>"Unit Test Brand Patch");
        $body = json_encode($body_contents);
        $endpoint = "/brands/1";

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
        $body_contents = array("brandName"=>"Unit Test Brand Patch");
        $body = json_encode($body_contents);
        $endpoint = "/brands/1";

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
        $body_contents = array("brandName"=>"Unit Test Brand Patch");
        $body = json_encode($body_contents);
        $endpoint = "/brands/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
            $outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotEquals("Insert failed", $output);
        $this->assertEquals(1, $outputJSON->brandID);
        $this->assertEquals("Unit Test Brand Patch", $outputJSON->brandName);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());

        $this->revertFromPatchTests();
    }
    public function testPatchInvalidBrandNameCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("brandName"=>"");
        $body = json_encode($body_contents);
        $endpoint = "/brands/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals("Invalid data in request body", $output);
        $this->assertEquals(StatusCodes::BAD_REQUEST, Testing::getLastHTTPResponseCode());
    }
    public function testPatchInvalidBrandIDCURL()
    {
        $token = $this->generateToken("genericAdmin", "Admin");
        $body_contents = array("brandName"=>"La de Dah");
        $body = json_encode($body_contents);
        $endpoint = "/brands/9000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
            //$outputJSON = (object)json_decode($output);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertEquals("Brand not found", $output);
        $this->assertEquals(StatusCodes::NOT_FOUND, Testing::getLastHTTPResponseCode());
    }


    //Test DELETE
    //Test: No token
    public function testDeleteNoTokenCURL(){
        $token = "";
        $body = "";
        $endpoint = "/brands/1";

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
        $brandCtrl = new BrandsController();

        $output = $brandCtrl->deleteBrand(1, $token);

        $this->assertEquals("Improper Role", $output, "Improper role failed to trigger");
    }*/
    public function testDeleteDevTokenCURL(){
        $token = $this->generateToken("genericDev", "Dev");
        $body = "";
        $endpoint = "/brands/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No brands in database";
        }

        $this->assertEquals("Improper Role", $output);
        $this->assertEquals(StatusCodes::FORBIDDEN, Testing::getLastHTTPResponseCode());
    }
    //Test: Admin token
    public function testDeleteAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $brandCtrl = new BrandsController();

        $output = $brandCtrl->deleteBrand(1, $token);

        $this->assertEquals("Success", $output, "Delete failed");
        $this->restoreDeleted();
    }
    public function testDeleteAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/brands/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No brands in database";
        }

        $this->assertEquals('"Success"', $output);
        $this->assertEquals(StatusCodes::OK, Testing::getLastHTTPResponseCode());
    }

    public function testDeleteInvalidAdminToken(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $brandCtrl = new BrandsController();

        $output = $brandCtrl->deleteBrand(9000000, $token);

        $this->assertEquals("Success", $output, "Delete failed");
        $this->restoreDeleted();
    }
    public function testDeleteInvalidAdminTokenCURL(){
        $token = $this->generateToken("genericAdmin", "Admin");
        $body = "";
        $endpoint = "/brands/9000000";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::DELETE, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
            $output = "No brands in database";
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
        $body_contents = array("brandName"=>"Unit Test Brand");
        $body = json_encode($body_contents);
        $endpoint = "/brands/1";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::PATCH, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }
    }
    private function restoreDeleted(){
        $brand = new Brand();
        $brand->setBrandName("Unit Test Brand");
        $brand->createBrand();
    }
    private function deleteTestBrand(){
        $brand = new Brand(1);
        $brand->deleteBrand();
    }
}