<?php

namespace src\services\address;

use Src\db\DBConnector;
use Src\utils\KeyChecker;
use Src\Keys;
use Src\HttpStatusCode;

class ListAddressV1 {
    private $addressId;
    public static $address;

    public function __construct($id){
        $this->addressId = $id;
        if(isset($this->addressId)) {
            
            $connection = DBConnector::get_connection();
            $query = 'SELECT "id", "street_line_1", "area", "locality", "house_no", "post_office", "state", "district", "sub_district", "city", "pincode", "created_at", "updated_at"
                FROM "address" WHERE ID = :id';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->addressId));
                $result = $statement->fetch(\PDO::FETCH_ASSOC);

                if(!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }

                ListAddressV1::$address = json_encode($result);

            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}