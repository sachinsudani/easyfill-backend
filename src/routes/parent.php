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

use Src\services\parent\CreateParentV1;
use Src\services\parent\DeleteParentV1;
use Src\services\parent\UpdateParentV1;
use Src\services\parent\ListParentV1;

$route = new RouteHandler();

$route->route("GET", "/user/parent", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $parentId = json_decode(ListUserV1::$user)->parent_id;

    if($parentId != null) {
        $parent = new ListParentV1($parentId);
        $parentArray = json_decode(ListParentV1::$parent, true);

        if(isset($parentArray["name_id"])) {
            $name = new ListNameV1($parentArray["name_id"]);
            $nameArray = json_decode(ListNameV1::$name);
        
            $parentArray["name"] = $nameArray;
            unset($parentArray["name_id"]);
        }
        echo json_encode($parentArray);
    }
});

$route->route("POST", "/user/parent", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $parentArray = json_decode(file_get_contents('php://input'), true);
    $nameId = null;

    $user = new ListUserV1($authenticate[1]['id']);
    $parentId = json_decode(ListUserV1::$user)->parent_id;

    if(!isset($parentId)) {
        if(isset($parentArray["name"])) {
            $name = new CreateNameV1(json_encode($parentArray["name"]));
            $nameId = json_decode(CreateNameV1::$lastInsertedName)->id;
            unset($parentArray["name"]);
        }
    
        $parent = new CreateParentV1($nameId, json_encode($parentArray));
        $parentId = json_decode(CreateParentV1::$lastInsertedParent)->id;
    
        $user = new UpdateUserV1($authenticate[1]['id'], '{"parent_id": ' . $parentId . '}');
        
        if($nameId != null) {
            $parentArray = json_decode(CreateParentV1::$lastInsertedParent, true);
            $nameArray = json_decode(CreateNameV1::$lastInsertedName, true);
    
            $parentArray["name"] = $nameArray;
            unset($parentArray["name_id"]);
    
            echo json_encode($parentArray);
            exit();
        }
    
        echo CreateParentV1::$lastInsertedParent;
        exit();
    }

    header(HttpStatusCode::CONFLICT);
    echo '{"error": "parent already exist"}';
    exit();
    
});

$route->route("PUT", "/user/parent", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $parentId = json_decode(ListUserV1::$user)->parent_id;

    $parentArray = json_decode(file_get_contents('php://input'), true);
    $nameArray = null;

    if(isset($parentArray["name"])) {
        $nameArray = $parentArray["name"];
        unset($parentArray["name"]);
    }

    new UpdateParentV1($parentId, json_encode($parentArray));
    $nameId = json_decode(UpdateParentV1::$lastUpdatedParent)->name_id;
    
    if($nameArray != null && $nameId != null) {
        new UpdateNameV1($nameId, json_encode($nameArray));    
    }
    
    $parent = new ListParentV1($parentId);
    $parentArray = json_decode(ListParentV1::$parent, true);

    if(isset($parentArray["name_id"])) {
        $name = new ListNameV1($parentArray["name_id"]);
        $nameArray = json_decode(ListNameV1::$name);
    
        $parentArray["name"] = $nameArray;
        unset($parentArray["name_id"]);
    }
    echo json_encode($parentArray);

});

$route->route("DELETE", "/user/parent", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $parentId = json_decode(ListUserV1::$user)->parent_id;

    if(isset($parentId)) {
        $parent = new ListParentV1($parentId);
        $nameId = json_decode(ListParentV1::$parent)->name_id;

        new UpdateParentV1($parentId, '{"name_id": null}');
        new DeleteNameV1($nameId);

        new UpdateUserV1($authenticate[1]['id'], '{"parent_id": null}');
        new DeleteParentV1($parentId); 
        exit();       
    }

    header(HttpStatusCode::NOT_FOUND);
    exit();
});

if(!($route->matchRoute() >= 1)) {
    $isNotFound = true;
}