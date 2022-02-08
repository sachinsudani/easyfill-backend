<?php

namespace src\services;

use Src\db\DBConnector;
use Src\Helper\KeyChecker;
use Src\Keys;
use Src\HttpStatusCode;

class CreateUserV1 {
    private $user;
    public static $lastInsertedUser;

    public function __construct($json){
        $this->user = json_decode($json, true);
        if(keyChecker::validate(keys::$user, $this->user)) {
            
            $connection = DBConnector::get_connection();
            $query = "INSERT INTO `user` (`username`, `password`, `email`, `contact_no`, `dob`) 
            VALUES (:username, :password, :email, :contact_no, :dob)";

            try {
                $statement = $connection->prepare($query);
                $statement->execute(
                    array(
                        'username' => $this->user["username"],
                        'password' => $this->user["password"],
                        'email' => $this->user["email"],
                        'contact_no' => isset($this->user["contact_no"]) ? $this->user["contact_no"] : "",
                        'dob' => isset($this->user["dob"]) ? $this->user["dob"] : ""
                    )
                );
                $inserted_user_id = $connection->lastInsertId();
                
                $user = "SELECT `id`, `username`, `password`, `contact_no`, `dob` FROM `user` WHERE id = :id";

                $userStatement = $connection->prepare($user);
                $userStatement->execute(array('id' => $inserted_user_id));
                $result = $userStatement->fetch(\PDO::FETCH_ASSOC);

                CreateUserV1::$lastInsertedUser = json_encode($result);

            } catch(\PDOException $ex) {
                if(str_contains($ex->getMessage(), "Duplicate entry")) {
                    header(HttpStatusCode::CONFLICT);
                    exit();
                }
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}