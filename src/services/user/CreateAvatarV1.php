<?php

namespace src\services\user;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class CreateAvatarV1 {
    private $user; 
    public static $lastUpdatedUser;

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
                
                $user = 'SELECT "id", "username", "password", "contact_no", "dob", "image" FROM "user" WHERE id = :id';

                $userStatement = $connection->prepare($user);
                $userStatement->execute(array('id' => $id));
                $result = $userStatement->fetch(\PDO::FETCH_ASSOC);

                CreateAvatarV1::$lastUpdatedUser = json_encode($result);

                header(HttpStatusCode::CREATED);
                
            } catch(\PDOException $ex) {
                if(str_contains($ex->getMessage(), "duplicate key")) {
                    header(HttpStatusCode::CONFLICT);
                    exit();
                }
                echo $ex->getMessage();
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}