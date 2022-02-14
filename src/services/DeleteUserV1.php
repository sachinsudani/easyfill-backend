<?php

namespace src\services;

use Src\db\DBConnector;
use Src\Helper\KeyChecker;
use Src\Keys;
use Src\HttpStatusCode;

class DeleteUserV1 {
    private $userId;
    public static $lastDeletedUser;

    public function __construct($json){
        $this->userId = json_decode($json, true);
        if(keyChecker::validate(keys::$universalDelete, $this->userId)) {
            
            $connection = DBConnector::get_connection();
            $query = "DELETE FROM `user` WHERE id = :id;";

            try {
                
                $user = "SELECT `id`, `username`, `password`, `contact_no`, `dob` FROM `user` WHERE id = :id";

                $userStatement = $connection->prepare($user);
                $userStatement->execute(array('id' => $this->userId["id"]));
                $result = $userStatement->fetch(\PDO::FETCH_ASSOC);

                DeleteUserV1::$lastDeletedUser = json_encode($result);

                if(!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }
                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->userId["id"]));

                header(HttpStatusCode::DELETE);
                
            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}