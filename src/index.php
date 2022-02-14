<?php

    require "../config.php";
    use Src\services\CreateUserV1;
    use Src\services\DeleteUserV1;

    $user = new DeleteUserV1('{"id": "3"}');
    echo DeleteUserV1::$lastDeletedUser;