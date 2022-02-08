<?php

    require "../config.php";
    use Src\services\CreateUserV1;

    $user = new CreateUserV1('{"username": "jkl", "password": "123", "email": "mno", "contact_no": "1234567890"}');
    echo CreateUserV1::$lastInsertedUser;