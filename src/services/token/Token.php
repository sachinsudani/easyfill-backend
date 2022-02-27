<?php

namespace src\services\token;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class Token {
    private $userId;
    public $isRepeated = false;

    public function __construct($userId){
        $this->userId = $userId;
    }

    public function setToken($token) {
        $connection = DBConnector::get_connection();
        $query = 'INSERT INTO "token" ("jwt", "user_id") VALUES (:token, :id)';

        try {
            $statement = $connection->prepare($query);
            $statement->execute(
                array(
                    'token' => $token,
                    'id' => $this->userId
                )
            );
        } catch(\PDOException $ex) {
            if(str_contains($ex->getMessage(), "duplicate key")) {
                $this->isRepeated = "true";
            }
        }
    }

    public function getToken()
    {
        $connection = DBConnector::get_connection();
        $token = 'SELECT "id", "jwt", "user_id" FROM "token" WHERE user_id = :id';

        $tokenStatement = $connection->prepare($token);
        $tokenStatement->execute(array('id' => $this->userId));
        $result = $tokenStatement->fetch(\PDO::FETCH_ASSOC);

        if(!is_bool($result)) {
            $token = array();
            $token["token"] = $result["jwt"];
            return json_encode($token);
        }

        return $result;
    }

    public function isTokenInDatabase() {
        $token = $this->getToken();

        if(!is_bool($token)) {
            return true;
        } else {
            return false;
        }
    }

    public function removedToken()
    {
        $connection = DBConnector::get_connection();
        $query = 'DELETE FROM "token" WHERE id = :id;';

        try {    
            $token = 'SELECT "id", "jwt", "user_id" FROM "token" WHERE user_id = :id';

            $tokenStatement = $connection->prepare($token);
            $tokenStatement->execute(array('id' => $this->userId));
            $result = $tokenStatement->fetch(\PDO::FETCH_ASSOC);

            if(!$result) {
                return true;
            }

            $statement = $connection->prepare($query);
            $statement->execute(array('id' => $result["id"]));

            return true;
        } catch(\PDOException $ex) {
            return false;
        }
    }

}