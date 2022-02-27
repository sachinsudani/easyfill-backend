<?php

namespace src\services\user;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class CreateAvatarV1 {
    private $user; 
    public function __construct($id, $data){
        if(isset($id) && isset($data)) {
            
            $connection = DBConnector::get_connection();
            $query = 'UPDATE "user" SET "image" = :image WHERE id = :id';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(
                    array(
                        'image' => $data,
                        'id' => $id
                    )
                );

                header(HttpStatusCode::CREATED);
                
            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}