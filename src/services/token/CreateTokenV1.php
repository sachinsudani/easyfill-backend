<?php

namespace src\services\token;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class CreateTokenV1 {
    private $userId;
    public $isRepeated = false;

    public function __construct($token, $userId){
        if(isset($token) && isset($userId)) {
            $this->userId = $userId;
            $connection = DBConnector::get_connection();
            $query = 'INSERT INTO "token" ("jwt", "user_id") VALUES (:token, :id)';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(
                    array(
                        'token' => $token,
                        'id' => $userId
                    )
                );

            } catch(\PDOException $ex) {
                if(str_contains($ex->getMessage(), "duplicate key")) {
                    $this->isRepeated = "true";
                }
                // header(HttpStatusCode::BAD_REQUEST);
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

        $token = array();
        $token["token"] = $result["jwt"];
        $this->token = json_encode($token);
        return $this->token;
    }

}