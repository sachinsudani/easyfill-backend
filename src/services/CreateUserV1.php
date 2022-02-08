<?php

use Src\db\DBConnector;
use Src\Helper\KeyChecker;
use Src\Keys;
require "../../config.php";

$connection = DBConnector::get_connection();

class CreateUserV1 {

    private $user;

    public function __construct($json){
        $this->user = json_decode($json, true);
        if(keyChecker::validate(keys::$user, $this->user)) {
            echo "success";
        }
    }
}