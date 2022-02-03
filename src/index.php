<?php

    use Src\db\DBConnector;
    require "./loadEnv.php";

    $connection = DBConnector::get_connection();
    print_r($connection);