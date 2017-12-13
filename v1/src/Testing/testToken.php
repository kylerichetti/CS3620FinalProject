<?php

namespace BowlingBall\Testing;

use BowlingBall\Controllers\TokensController;
use BowlingBall\Models\Token;
use BowlingBall\Http\Methods;
use BowlingBall\Utilities\Testing;
use \PHPUnit\Framework\TestCase;


class TokenTest extends TestCase {
    public function testPostAsDeveloper()
    {
        $token = $this->generateToken('genericDev', 'Dev');

        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_DEV, Token::getRoleFromToken($token));
    }

    public function testPostAsAdmin()
    {
        $token = $this->generateToken('genericAdmin', 'Admin');

        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_ADMIN, Token::getRoleFromToken($token));
    }

    private function generateToken($username, $password)
    {
        $tokenController = new TokensController();
        return $tokenController->buildToken($username, $password);
    }

    public function testCurl()
    {
        $token = "";
        $body_contents = array("username"=>"genericAdmin", "password"=>"Admin");
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