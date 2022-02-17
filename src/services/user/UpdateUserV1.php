<?php

namespace src\services\user;

use Src\db\DBConnector;
use Src\Helper\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class UpdateUserV1 {
    private $user; 
    public static $lastUpdatedUser;

    public function __construct($id, $json){
        $this->user = JsonValidator::pretify($json, AllSchemas::$user);
        if(JsonValidator::validate($this->user, AllSchemas::$user)) {
            
            $connection = DBConnector::get_connection();
            $query = "UPDATE `user` SET 
                `username` = :username, 
                `password` = :password, 
                `email` = :email, 
                `contact_no` = :contact_no, 
                `dob` = :dob
            WHERE ID = :id"; 

            try {
                $statement = $connection->prepare($query);
                $statement->execute(
                    array(
                        'username' => $this->user["username"],
                        'password' => $this->user["password"],
                        'email' => $this->user["email"],
                        'contact_no' => isset($this->user["contact_no"]) ? $this->user["contact_no"] : "",
                        'dob' => isset($this->user["dob"]) ? $this->user["dob"] : "",
                        'id' => $id
                    )
                );
                
                $user = "SELECT `id`, `username`, `password`, `contact_no`, `dob` FROM `user` WHERE id = :id";

                $userStatement = $connection->prepare($user);
                $userStatement->execute(array('id' => $id));
                $result = $userStatement->fetch(\PDO::FETCH_ASSOC);

                UpdateUserV1::$lastUpdatedUser = json_encode($result);

                header(HttpStatusCode::CREATED);
                exit();
                
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