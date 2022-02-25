<?php

namespace src\services\user;

use Src\db\DBConnector;
use Src\utils\KeyChecker;
use Src\Keys;
use Src\HttpStatusCode;

class GetAvatarV1 {
    private $userId;
    public static $user;

    public function __construct($id){
        $this->userId = $id;
        if(isset($this->userId)) {
            
            $connection = DBConnector::get_connection();
            $query = 'SELECT "image" FROM "user" WHERE ID = :id';

            try {
                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->userId));
                $result = $statement->fetch(\PDO::FETCH_ASSOC);
 
                
                if(!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }
                
                GetAvatarV1::$user = stream_get_contents($result["image"]);

            } catch(\PDOException $ex) {
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }

        }
    }
}