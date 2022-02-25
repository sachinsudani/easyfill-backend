<?php

namespace src\services\name;

use Src\db\DBConnector;
use Src\utils\Validator;
use Src\Schema;
use Src\HttpStatusCode;

class DeleteNameV1 {
    private $nameId;

    public function __construct($id){
        $this->nameId = $id;
        if(isset($this->nameId)) {
            
            $connection = DBConnector::get_connection();
            $query = 'DELETE FROM "name" WHERE id = :id;';

            try {
                
                $name = 'SELECT "id", "firstname", "lastname", "middlename", "fullname" FROM "name" WHERE id = :id';

                $nameStatement = $connection->prepare($name);
                $nameStatement->execute(array('id' => $this->nameId));
                $result = $nameStatement->fetch(\PDO::FETCH_ASSOC);

                if(!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }

                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->nameId));

                header(HttpStatusCode::DELETE);
                
            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}