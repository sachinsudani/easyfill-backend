<?php

namespace src\services\parent;

use Src\db\DBConnector;
use Src\utils\Validator;
use Src\Schema;
use Src\HttpStatusCode;

class DeleteParentV1 {
    private $parentId;

    public function __construct($id){
        $this->parentId = $id;
        if(isset($this->parentId)) {
            
            $connection = DBConnector::get_connection();
            $query = 'DELETE FROM "parent" WHERE id = :id;';

            try {
                
                $parent = 'SELECT "id", "relation", "name_id" FROM "parent" WHERE id = :id';

                $parentStatement = $connection->prepare($parent);
                $parentStatement->execute(array('id' => $this->parentId));
                $result = $parentStatement->fetch(\PDO::FETCH_ASSOC);

                if(!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }

                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->parentId));

                header(HttpStatusCode::DELETE);
                
            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}