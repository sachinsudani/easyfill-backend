<?php

namespace src\services\token;

use Src\db\DBConnector;
use Src\utils\Validator;
use Src\Schema;
use Src\HttpStatusCode;

class DeleteTokenV1 {
    private $tokenId;

    public function __construct($id){
        $this->tokenId = $id;
        if(isset($this->tokenId)) {
            
            $connection = DBConnector::get_connection();
            $query = 'DELETE FROM "token" WHERE id = :id;';

            try {
                
                $token = 'SELECT "jwt", "user_id" FROM "token" WHERE id = :id';

                $tokenStatement = $connection->prepare($token);
                $tokenStatement->execute(array('id' => $this->tokenId));
                $result = $tokenStatement->fetch(\PDO::FETCH_ASSOC);

                if(!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }

                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->tokenId));

                header(HttpStatusCode::DELETE);
                
            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}