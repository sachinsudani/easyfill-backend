<?php

namespace src\schema;

use Src\utils\SchemaGenerator;

include(__DIR__. "/address.php");
include(__DIR__. "/addressUpdate.php");
include(__DIR__. "/user.php");
include(__DIR__. "/userLogin.php");
include(__DIR__. "/userUpdate.php");
include(__DIR__. "/name.php");
include(__DIR__. "/nameUpdate.php");
include(__DIR__. "/parent.php");
include(__DIR__. "/parentUpdate.php");

class AllSchemas
{
    public static $user;
    public static $userLogin;
    public static $userUpdate;
    
    public static $name;
    public static $nameUpdate;
    
    public static $parent;
    public static $parentUpdate;

    public static $address;
    public static $addressUpdate;
    
    public function __construct($user, $userLogin, $userUpdate, $name, $nameUpdate, $parent, $parentUpdate, $address, $addressUpdate) {
        static::$user = new SchemaGenerator($user);
        static::$address = new SchemaGenerator($address);
        static::$userLogin = new SchemaGenerator($userLogin);
        static::$userUpdate = new SchemaGenerator($userUpdate);
        
        static::$name = new SchemaGenerator($name);
        static::$nameUpdate = new SchemaGenerator($nameUpdate);

        static::$parent = new SchemaGenerator($parent);
        static::$parentUpdate = new SchemaGenerator($parentUpdate);
      
        static::$address = new SchemaGenerator($address);
        static::$addressUpdate = new SchemaGenerator($addressUpdate);
    }
}

new AllSchemas(
    $userSchema, 
    $userLoginSchema, 
    $userUpdateSchema, 
    $nameSchema, 
    $nameUpdateSchema, 
    $parentSchema, 
    $parentUpdateSchema, 
    $addressSchema,
    $addressUpdateSchema
);