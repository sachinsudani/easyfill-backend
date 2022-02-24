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
                        'street_line_1' => $this->address["street_line_1"],
                        'area' => $this->address["area"],
                        'locality' => $this->address["locality"],
                        'house_no' => $this->address["house_no"],
                        'post_office' => $this->address["post_office"],
                        'state' => $this->address["state"],
                        'district' => $this->address["district"],
                        'sub_district' => $this->address["sub_district"],
                        'city' => $this->address["city"],
                        'pincode' => $this->address["pincode"],

                    )
                );

                $address = 'SELECT * FROM "address" WHERE id = :id';
                $inserted_address_id = $connection->lastInsertId();

                $userStatement = $connection->prepare($address);
                $userStatement->execute(array('id' => $inserted_address_id));
                $result = $userStatement->fetch(\PDO::FETCH_ASSOC);

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
