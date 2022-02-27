<?php

namespace src\services\name;

use Src\db\DBConnector;
use Src\utils\KeyChecker;
use Src\Keys;
use Src\HttpStatusCode;

class ListNameV1 {
    private $nameId;
    public static $name;

    public function __construct($id){
        $this->nameId = $id;
        if(isset($this->nameId)) {
            
            $connection = DBConnector::get_connection();
            $query = 'SELECT "id", "firstname", "lastname", "middlename", "fullname", "created_at", "updated_at"
                FROM "name" WHERE ID = :id';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->nameId));
                $result = $statement->fetch(\PDO::FETCH_ASSOC);

                if(!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }

                ListNameV1::$name = json_encode($result);

            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}