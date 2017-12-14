<?php
/**
 * Created by PhpStorm.
 * User: Joshua
 * Date: 10/24/2016
 * Time: 12:55 PM
 */

namespace BowlingBall\Controllers;

use \BowlingBall\Models\Token as Token;
use \BowlingBall\Models\User;
use \BowlingBall\Http\StatusCodes as StatusCodes;


class TokensController
{
    public function buildToken(string $username, string $password)
    {
        //Create user
        $user = new User($username, $password);

        //Populate user
        $auth = $user->authenticate();

        //If user exists
        if($auth['userExists']) {
            //Check if password is correct
            if($auth['correctPassword']) {
                //If yes, build token
                return (new Token())->buildToken($user->getRole(), $username);
            }
        }
        //Invalid username
        http_response_code(StatusCodes::UNAUTHORIZED);
        exit("Username and/or password incorrect");
    }

}
