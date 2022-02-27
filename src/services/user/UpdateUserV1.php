<?php

namespace src\services\user;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class UpdateUserV1 {
    private $user; 
    public static $lastUpdatedUser;

    public function __construct($id, $json){
        $this->user = JsonValidator::pretify($json, AllSchemas::$userUpdate);
        
        if(JsonValidator::validate($this->user, AllSchemas::$userUpdate)) {
            
            if(!(count($this->user) >= 1)) {
                exit();
            }
            
            $query = 'UPDATE "user" SET ';
            $lastelement = end($this->user);
            
            foreach ($this->user as $key => $value) {
                if($value == $lastelement) {
                    $query = $query . '"'. $key . '" = :' . $key;
                }
                else {
                    $query = $query . '"'. $key . '" = :' . $key . ',';
                }
            }
            
            $query = $query . ' WHERE "id" = :id';
            
            $this->user["id"] = $id;
            if(isset($this->user["password"]))
                $this->user["password"] = password_hash($this->user["password"], PASSWORD_BCRYPT);
            
            if(isset($this->user["gender"]))
                $this->user["gender"] = $this->user["gender"] == "male" ? "m" : "f";
            
            $connection = DBConnector::get_connection();
            
            try {
                $statement = $connection->prepare($query);
                $statement->execute($this->user);
                
                $user = 'SELECT "id", "username", "email", "contact_no", "dob", "gender", "address_id", "name_id", "parent_id", "created_at", "updated_at" FROM "user" WHERE id = :id';
                
                $userStatement = $connection->prepare($user);
                $userStatement->execute(array('id' => $id));
                $result = $userStatement->fetch(\PDO::FETCH_ASSOC);

                UpdateUserV1::$lastUpdatedUser = json_encode($result);

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