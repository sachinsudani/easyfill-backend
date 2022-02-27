<?php

use Src\utils\RouteHandler;
use Src\utils\UserAuth;
use Src\HttpStatusCode;

use Src\services\address\CreateAddressV1;
use Src\services\address\DeleteAddressV1;
use Src\services\address\UpdateAddressV1;
use Src\services\address\ListAddressV1;

use Src\services\user\ListUserV1;
use Src\services\user\UpdateUserV1;

$route = new RouteHandler();

$route->route("GET", "/user/address", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $addressId = json_decode(ListUserV1::$user)->address_id;

    if($addressId != null) {
        $name = new ListAddressV1($addressId);
        echo ListAddressV1::$address;
    }
});

$route->route("POST", "/user/address", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $addressId = json_decode(ListUserV1::$user)->address_id;

    if(!isset($addressId)) {
        new CreateAddressV1(file_get_contents('php://input'));
        $addressId = json_decode(CreateAddressV1::$lastInsertedAddress)->id;
        new UpdateUserV1($authenticate[1]['id'], '{"address_id": ' . $addressId . '}');
        echo CreateAddressV1::$lastInsertedAddress;
        exit();
    }

    header(HttpStatusCode::CONFLICT);
    echo '{"error": "address already exist"}';
    exit();
});

$route->route("PUT", "/user/address", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $addressId = json_decode(ListUserV1::$user)->address_id;
    
    new UpdateAddressV1($addressId, file_get_contents('php://input'));
    echo UpdateAddressV1::$lastUpdatedAddress;
    
});

$route->route("DELETE", "/user/address", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $addressId = json_decode(ListUserV1::$user)->address_id;

    if($addressId != null) {
        new UpdateUserV1($authenticate[1]['id'], '{"address_id": null}');
        new DeleteAddressV1($addressId);
    }
});

if(!($route->matchRoute() >= 1)) {
    $isNotFound = true;
}
