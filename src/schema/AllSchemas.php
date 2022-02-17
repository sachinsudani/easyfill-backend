<?php

namespace src\schema;

use Src\Helper\SchemaGenerator;

include(__DIR__. "\user.php");

class AllSchemas {
    public static $user;

    public function __construct($user) {
        static::$user = new SchemaGenerator($user);
    }
}

new AllSchemas($userSchema);