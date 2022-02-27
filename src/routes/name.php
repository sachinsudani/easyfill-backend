<?php

use Src\utils\RouteHandler;
use Src\utils\UserAuth;
use Src\HttpStatusCode;

use Src\services\name\CreateNameV1;
use Src\services\name\DeleteNameV1;
use Src\services\name\UpdateNameV1;
use Src\services\name\ListNameV1;

use Src\services\user\ListUserV1;
use Src\services\user\UpdateUserV1;

$route = new RouteHandler();

$route->route("GET", "/user/name", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $nameId = json_decode(ListUserV1::$user)->name_id;

    if($nameId != null) {
        new ListNameV1($nameId);
        echo ListNameV1::$name;
    }
});

$route->route("POST", "/user/name", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $nameId = json_decode(ListUserV1::$user)->name_id;

    if(!isset($nameId)) {
        $name = new CreateNameV1(file_get_contents('php://input'));
        $nameId = json_decode(CreateNameV1::$lastInsertedName)->id;
        new UpdateUserV1($authenticate[1]['id'], '{"name_id": ' . $nameId . '}');
        echo CreateNameV1::$lastInsertedName;
        exit();
    }

    header(HttpStatusCode::CONFLICT);
    echo '{"error": "name already exist"}';
    exit();
});

$route->route("PUT", "/user/name", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $nameId = json_decode(ListUserV1::$user)->name_id;
    
    $name = new UpdateNameV1($nameId, file_get_contents('php://input'));
    echo UpdateNameV1::$lastUpdatedName;
});

$route->route("DELETE", "/user/name", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $nameId = json_decode(ListUserV1::$user)->name_id;

    if($nameId != null) {
        $user = new UpdateUserV1($authenticate[1]['id'], '{"name_id": null}');
        echo UpdateUserV1::$lastUpdatedUser;
        new DeleteNameV1($nameId);
    }
});

if(!($route->matchRoute() >= 1)) {
    $isNotFound = true;
}