<?php

namespace src\services\name;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class UpdateNameV1 {
    private $name; 
    public static $lastUpdatedName;

    public function __construct($id, $json){
        $this->name = JsonValidator::pretify($json, AllSchemas::$nameUpdate);
        
        if(JsonValidator::validate($this->name, AllSchemas::$nameUpdate)) {
            
            if(!(count($this->name) >= 1)) {
                exit();
            }
            
            $query = 'UPDATE "name" SET ';
            $count = count($this->name);
            
            $i = 0;
            foreach ($this->name as $key => $value) {
                if(++$i == $count) {
                    $query = $query . '"'. $key . '" = :' . $key;
                }
                else {
                    $query = $query . '"'. $key . '" = :' . $key . ',';
                }
            }
            
            $query = $query . ' WHERE "id" = :id';
            $this->name["id"] = $id;
            echo $query;
            
            $connection = DBConnector::get_connection();

            try {
                $statement = $connection->prepare($query);
                $statement->execute($this->name);
                
                $name = 'SELECT "id", "firstname", "lastname", "middlename", "fullname", "created_at", "updated_at"
                FROM "name" WHERE ID = :id';

                $nameStatement = $connection->prepare($name);
                $nameStatement->execute(array('id' => $id));
                $result = $nameStatement->fetch(\PDO::FETCH_ASSOC);

                UpdateNameV1::$lastUpdatedName = json_encode($result);

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