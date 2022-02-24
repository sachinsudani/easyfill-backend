<?php

namespace src\schema;

use Src\utils\SchemaGenerator;

include(__DIR__ . "\user.php");
include(__DIR__ . "\address.php");
include(__DIR__ . "\userLogin.php");

class AllSchemas
{
    public static $user;
    public static $address;
    public static $userLogin;

    public function __construct($user, $userLogin, $address)
    {
        static::$user = new SchemaGenerator($user);
        static::$address = new SchemaGenerator($address);
        static::$userLogin = new SchemaGenerator($userLogin);
    }
}

new AllSchemas($userSchema, $userLoginSchema, $addressSchema);
