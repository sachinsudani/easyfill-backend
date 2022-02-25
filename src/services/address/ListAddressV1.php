<?php

namespace src\services\user;

use Src\db\DBConnector;
use Src\utils\KeyChecker;
use Src\Keys;
use Src\HttpStatusCode;

class ListUserV1 {
    private $userId;
    public static $user;

    public function __construct($id){
        $this->userId = $id;
        if(isset($this->userId)) {
            
            $connection = DBConnector::get_connection();
            $query = 'SELECT "id", "username", "email", "contact_no", "dob", "created_at", "updated_at"
                FROM "user" WHERE ID = :id';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->userId));
                $result = $statement->fetch(\PDO::FETCH_ASSOC);

                if(!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }

                ListUserV1::$user = json_encode($result);

            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}