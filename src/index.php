<?php

    require __DIR__ . "/../config.php";
    use Src\services\user\CreateUserV1;
    use Src\services\user\UpdateUserV1;

    $user = new CreateUserV1('{"username": "abcjklm", "password": "abc", "email": "ablcj@email.com", "contact_no": "1234567890", "dob": "2000/10/10"}');
    echo CreateUserV1::$lastInsertedUser;