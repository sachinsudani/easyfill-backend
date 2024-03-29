<?php

namespace src\utils;

use Firebase\JWT\JWT;
use Src\services\token\Token;

class UserAuth
{
    private static $key;
    private $user;
    private static $result = array(false, '');

    public function __construct($user = null)
    {
        $this->user = $user;
        static::$key = getenv("JSON_KEY");
    }

    // Generate Authentication Token
    private function genJWT()
    {
        $payload = array(
            "id" => $this->user['id'],
            "username" => $this->user['username'],
        );

        return JWT::encode($payload, UserAuth::$key);
    }

    // return token
    public function get_token()
    {
        $token = $this->genJWT();
        return $token;
    }

    // Validates a token
    public static function validateJWT()
    {
        if (isset(getallheaders()['Authorization'])) {
            $authorizationHeader = explode(" ", getallheaders()['Authorization']);
            if (!(isset($authorizationHeader[1]) && count($authorizationHeader) == 2)) {
                header("HTTP/1.1 401 Unauthorised");
                exit();
            }

            $token = $authorizationHeader[1];

            try {
                $decode = JWT::decode($token, UserAuth::$key, array('HS256'));

                $token = new Token($decode->id);
                if($token->isTokenInDatabase()) {
                    UserAuth::$result['0'] = true;
                    UserAuth::$result['1'] = (array) $decode;
                } else {
                    throw new \Exception();
                }

            } catch (\Exception $ex) {
                echo $ex->getMessage();
                header("HTTP/1.1 401 Unauthorised");
                exit();
            }
        } else {
            header("HTTP/1.1 401 Unauthorised");
            exit();
        }

        return UserAuth::$result;
    }
}
