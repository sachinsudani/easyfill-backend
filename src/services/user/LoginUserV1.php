<?php

namespace src\services\user;

use Src\db\DBConnector;
use Src\utils\KeyChecker;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class LoginUserV1 {
    private $user;
    public static $isLogin = false;
    public static $userLoginData;

    public function __construct($json){
        $this->user = JsonValidator::pretify($json, AllSchemas::$userLogin);
        if(JsonValidator::validate($this->user, AllSchemas::$userLogin)) {
       
            if(isset($this->user)) {
                
                $connection = DBConnector::get_connection();
                $query = 'SELECT "id", "username", "password"
                    FROM "user" WHERE username = :username';

                try {
                    $statement = $connection->prepare($query);
                    $statement->execute(array(
                        "username" => $this->user["username"]
                    ));
                    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

                    if(!isset($result[0])) {
                        header(HttpStatusCode::BAD_REQUEST);  
                        exit(); 
                    }

                    if(password_verify($this->user["password"], $result[0]["password"])) {
                        LoginUserV1::$isLogin = true;
                        array_pop($result[0]);
                        LoginUserV1::$userLoginData = $result[0];
                    }

                } catch (\PDOException $ex) {
                    header(HttpStatusCode::BAD_REQUEST);
                    exit();
                }
            }
        }
    }
}