<?php

namespace src\services\address;

use Src\db\DBConnector;
use Src\HttpStatusCode;

class DeleteAddressV1
{
    private $addressId;

    public function __construct($id)
    {
        $this->addressId = $id;
        if (isset($this->addressId)) {

            $connection = DBConnector::get_connection();
            $query = 'DELETE FROM "address" WHERE id = :id;';

            try {

                $address = 'SELECT "id" FROM "address" WHERE id = :id';

                $addressStatement = $connection->prepare($address);
                $addressStatement->execute(array('id' => $this->addressId));
                $result = $addressStatement->fetch(\PDO::FETCH_ASSOC);

                if (!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }

                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->addressId));

                header(HttpStatusCode::DELETE);
            } catch (\PDOException $ex) {
                echo $ex->getMessage();
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }
        }
    }
}
