<?php

namespace src\services\parent;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class UpdateParentV1 {
    private $parent; 
    public static $lastUpdatedParent;

    public function __construct($id, $json){
        $this->parent = JsonValidator::pretify($json, AllSchemas::$parentUpdate);
        
        if(JsonValidator::validate($this->parent, AllSchemas::$parentUpdate)) {
            
            if(!(count($this->parent) >= 1)) {
                exit();
            }
            
            $query = 'UPDATE "parent" SET ';
            $lastelement = end($this->parent);
            
            foreach ($this->parent as $key => $value) {
                if($value == $lastelement) {
                    $query = $query . '"'. $key . '" = :' . $key;
                }
                else {
                    $query = $query . '"'. $key . '" = :' . $key . ',';
                }
            }
            
            $query = $query . ' WHERE "id" = :id';
            $this->parent["id"] = $id;
            
            $connection = DBConnector::get_connection();

            try {
                $statement = $connection->prepare($query);
                $statement->execute($this->parent);
                
                $parent = 'SELECT "id", "relation", "name_id", "created_at", "updated_at" FROM "parent" WHERE id = :id';

                $parentStatement = $connection->prepare($parent);
                $parentStatement->execute(array('id' => $id));
                $result = $parentStatement->fetch(\PDO::FETCH_ASSOC);

                UpdateParentV1::$lastUpdatedParent = json_encode($result);
                header(HttpStatusCode::CREATED);
                
            } catch(\PDOException $ex) {
                echo $ex->getMessage();
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