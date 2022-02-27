<?php

namespace src\services\parent;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class CreateParentV1 {
    private $parent; 
    public static $lastInsertedParent;

    public function __construct($id, $json){
        $this->parent = JsonValidator::pretify($json, AllSchemas::$parent);
        if(JsonValidator::validate($this->parent, AllSchemas::$parent)) {
            $this->parent["nameId"] = $id;
            $connection = DBConnector::get_connection();
            $query = 'INSERT INTO "parent" ("relation", "name_id") 
            VALUES (:relation, :name_id)';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(
                    array(
                        'relation' => $this->parent["relation"],
                        'name_id' => $this->parent["nameId"],
                    )
                );
                $inserted_parent_id = $connection->lastInsertId();
                
                $parent = 'SELECT "id", "relation", "name_id", "created_at", "updated_at" FROM "parent" WHERE id = :id';

                $parentStatement = $connection->prepare($parent);
                $parentStatement->execute(array('id' => $inserted_parent_id));
                $result = $parentStatement->fetch(\PDO::FETCH_ASSOC);

                CreateparentV1::$lastInsertedParent = json_encode($result);

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