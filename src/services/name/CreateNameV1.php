<?php

namespace src\services\name;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class CreateNameV1 {
    private $name; 
    public static $lastInsertedName;

    public function __construct($json){
        $this->name = JsonValidator::pretify($json, AllSchemas::$name);
        if(JsonValidator::validate($this->name, AllSchemas::$name)) {
            
            $connection = DBConnector::get_connection();
            $query = 'INSERT INTO "name" ("firstname", "lastname", "middlename", "fullname") 
            VALUES (:firstname, :lastname, :middlename, :fullname)';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(
                    array(
                        'firstname' => $this->name["firstname"],
                        'lastname' => $this->name["lastname"],
                        'middlename' => $this->name["middlename"],
                        'fullname' => $this->name["fullname"]
                    )
                );
                $inserted_name_id = $connection->lastInsertId();
                
                $name = 'SELECT "id", "firstname", "lastname", "middlename", "fullname" FROM "name" WHERE id = :id';

                $nameStatement = $connection->prepare($name);
                $nameStatement->execute(array('id' => $inserted_name_id));
                $result = $nameStatement->fetch(\PDO::FETCH_ASSOC);

                CreateNameV1::$lastInsertedName = json_encode($result);

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
