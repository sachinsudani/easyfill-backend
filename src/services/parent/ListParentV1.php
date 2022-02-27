<?php

namespace src\services\parent;

use Src\db\DBConnector;
use Src\utils\KeyChecker;
use Src\Keys;
use Src\HttpStatusCode;

class ListParentV1 {
    private $parentId;
    public static $parent;

    public function __construct($id){
        $this->parentId = $id;
        if(isset($this->parentId)) {
            
            $connection = DBConnector::get_connection();
            $query = 'SELECT "id", "relation", "name_id","created_at", "updated_at"
                FROM "parent" WHERE ID = :id';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->parentId));
                $result = $statement->fetch(\PDO::FETCH_ASSOC);

                if(!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }

                ListParentV1::$parent = json_encode($result);

            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}