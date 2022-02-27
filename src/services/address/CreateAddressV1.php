<?php

namespace src\services\address;

use Src\db\DBConnector;
use Src\utils\JsonValidator;
use Src\schema\AllSchemas;
use Src\HttpStatusCode;

class CreateAddressV1
{
    private $address;
    public static $lastInsertedAddress;

    public function __construct($json)
    {
        $this->address = JsonValidator::pretify($json, AllSchemas::$address);
        if (JsonValidator::validate($this->address, AllSchemas::$address)) {

            $connection = DBConnector::get_connection();
            $query = 'INSERT INTO "address" ("street_line_1", "area", "locality", "house_no", "post_office","state","district","sub_district","city","pincode") 
            VALUES (:street_line_1, :area, :locality, :house_no, :post_office, :state, :district, :sub_district, :city, :pincode)';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(
                    array(
                        'street_line_1' => isset($this->address["street_line_1"]) ? $this->address["street_line_1"] : null,
                        'area' => isset($this->address["area"]) ? $this->address["area"] : null,
                        'locality' => isset($this->address["locality"]) ? $this->address["locality"] : null,
                        'house_no' => isset($this->address["house_no"]) ? $this->address["house_no"] : null,
                        'post_office' => isset($this->address["post_office"]) ? $this->address["post_office"] : null,
                        'state' => isset($this->address["state"]) ? $this->address["state"] : null,
                        'district' => isset($this->address["district"]) ? $this->address["district"] : null,
                        'sub_district' => isset($this->address["sub_district"]) ? $this->address["sub_district"] : null,
                        'city' => isset($this->address["city"]) ? $this->address["city"] : null,
                        'pincode' => isset($this->address["pincode"]) ? $this->address["pincode"] : null,

                    )
                );

                $address = 'SELECT "id", "street_line_1", "area", "locality", "house_no", "post_office", "state", "district", "sub_district", "city", "pincode", "created_at", "updated_at" FROM "address" WHERE id = :id';
                $inserted_address_id = $connection->lastInsertId();

                $addressStatement = $connection->prepare($address);
                $addressStatement->execute(array('id' => $inserted_address_id));
                $result = $addressStatement->fetch(\PDO::FETCH_ASSOC);

                CreateAddressV1::$lastInsertedAddress = json_encode($result);

                header(HttpStatusCode::CREATED);
            } catch (\PDOException $ex) {
                if (str_contains($ex->getMessage(), "duplicate key")) {
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
