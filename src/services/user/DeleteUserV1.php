<?php

namespace src\services\user;

use Src\db\DBConnector;
use Src\HttpStatusCode;

class DeleteUserV1
{
    private $userId;

    public function __construct($id)
    {
        $this->userId = $id;
        if (isset($this->userId)) {

            $connection = DBConnector::get_connection();
            $query = 'DELETE FROM "user" WHERE id = :id;';

            try {

                $user = 'SELECT "id", "username", "password", "contact_no", "dob" FROM "user" WHERE id = :id';

                $userStatement = $connection->prepare($user);
                $userStatement->execute(array('id' => $this->userId));
                $result = $userStatement->fetch(\PDO::FETCH_ASSOC);

                if (!$result) {
                    header(HttpStatusCode::NOT_FOUND);
                    exit();
                }

                $statement = $connection->prepare($query);
                $statement->execute(array('id' => $this->userId));

                header(HttpStatusCode::DELETE);
            } catch (\PDOException $ex) {
                echo $ex->getMessage();
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }
        }
    }
}
