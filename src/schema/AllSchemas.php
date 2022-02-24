<?php

namespace src\schema;

use Src\utils\SchemaGenerator;

include(__DIR__. "\user.php");
include(__DIR__. "\userLogin.php");

class AllSchemas {
    public static $user;
    public static $userLogin;

    public function __construct($user, $userLogin) {
        static::$user = new SchemaGenerator($user);
        static::$userLogin = new SchemaGenerator($userLogin);
    }
}

new AllSchemas($userSchema, $userLoginSchema);