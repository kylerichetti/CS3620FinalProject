<?php

namespace Scholarship\Testing;

use Scholarship\Controllers\TokensController;
use Scholarship\Models\Token;
use Scholarship\Http\Methods;
use Scholarship\Utilities\Testing;
use \PHPUnit\Framework\TestCase;


class TokenTest extends TestCase {
    public function testPostAsStudent()
    {
        $token = $this->generateToken('generic', 'Hello357');

        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_STUDENT, Token::getRoleFromToken($token));
    }

    public function testPostAsFaculty()
    {
        $token = $this->generateToken('genericfac', 'Hello896');

        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_FACULTY, Token::getRoleFromToken($token));
    }

    private function generateToken($username, $password)
    {
        $tokenController = new TokensController();
        return $tokenController->buildToken($username, $password);
    }

    public function testCurl()
    {
        $token = "";
        $body_contents = array("username"=>"genericfac", "password"=>"Hello896");
        $body = json_encode($body_contents);
        $endpoint = "/tokens";

        try {
            $output = Testing::callAPIOverHTTP($endpoint, Methods::POST, $body, $token, Testing::JSON);
        } catch (\Exception $err) {
            $this->assertEmpty($err->getMessage(), "Error message: ". $err->getMessage());
        }

        $this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.
        $this->assertEquals(200, Testing::getLastHTTPResponseCode());
        //$this->assertJsonStringEqualsJsonString(""); //Compare against expected JSON object. You  could also do other tests.
    }
}