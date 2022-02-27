<?php

namespace src\services\address;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class UpdateAddressV1 {
    private $address; 
    public static $lastUpdatedAddress;

    public function __construct($id, $json){
        $this->address = JsonValidator::pretify($json, AllSchemas::$addressUpdate);
        
        if(JsonValidator::validate($this->address, AllSchemas::$addressUpdate)) {
            
            if(!(count($this->address) >= 1)) {
                exit();
            }
            
            $query = 'UPDATE "address" SET ';
            $lastelement = end($this->address);
            
            foreach ($this->address as $key => $value) {
                if($value == $lastelement) {
                    $query = $query . '"'. $key . '" = :' . $key;
                }
                else {
                    $query = $query . '"'. $key . '" = :' . $key . ',';
                }
            }
            
            $query = $query . ' WHERE "id" = :id';
            $this->address["id"] = $id;
            
            $connection = DBConnector::get_connection();

            try {
                $statement = $connection->prepare($query);
                $statement->execute($this->address);
                
                $address = 'SELECT "id", "street_line_1", "area", "locality", "house_no", "post_office", "state", "district", "sub_district", "city", "pincode", "created_at", "updated_at" FROM "address" WHERE id = :id';

                $addressStatement = $connection->prepare($address);
                $addressStatement->execute(array('id' => $id));
                $result = $addressStatement->fetch(\PDO::FETCH_ASSOC);

                UpdateAddressV1::$lastUpdatedAddress = json_encode($result);
                header(HttpStatusCode::CREATED);
                
            } catch(\PDOException $ex) {
                echo $ex->getMessage();
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