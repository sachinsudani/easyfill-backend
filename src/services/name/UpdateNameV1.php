<?php

namespace src\services\name;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class UpdateNameV1 {
    private $name; 
    public static $lastUpdatedname;

    public function __construct($id, $json){
        $this->name = JsonValidator::pretify($json, AllSchemas::$nameUpdate);
        
        if(JsonValidator::validate($this->name, AllSchemas::$nameUpdate)) {
            
            if(!(count($this->name) >= 1)) {
                exit();
            }
            
            $query = 'UPDATE "name" SET ';
            $lastelement = end($this->name);
            
            foreach ($this->name as $key => $value) {
                if($value == $lastelement) {
                    $query = $query . '"'. $key . '" = :' . $key;
                }
                else {
                    $query = $query . '"'. $key . '" = :' . $key . ',';
                }
            }
            
            $query = $query . ' WHERE "id" = :id';
            $this->name["id"] = $id;
            
            $connection = DBConnector::get_connection();

            try {
                $statement = $connection->prepare($query);
                $statement->execute($this->name);
                
                $name = 'SELECT "firstname", "lastname", "middlename", "fullname" FROM "name" WHERE id = :id';

                $nameStatement = $connection->prepare($name);
                $nameStatement->execute(array('id' => $id));
                $result = $nameStatement->fetch(\PDO::FETCH_ASSOC);

                UpdateNameV1::$lastUpdatedname = json_encode($result);

                header(HttpStatusCode::CREATED);
                
            } catch(\PDOException $ex) {
                if(str_contains($ex->getMessage(), "duplicate key")) {
                    header(HttpStatusCode::CONFLICT);
                    exit();
                }
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}