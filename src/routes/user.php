<?php

use Src\utils\RouteHandler;
use Src\utils\UserAuth;
use Src\HttpStatusCode;

use Src\services\user\CreateUserV1;
use Src\services\user\UpdateUserV1;
use Src\services\user\ListUserV1;
use Src\services\user\DeleteUserV1;
use Src\services\user\LoginUserV1;
use Src\services\user\CreateAvatarV1;
use Src\services\user\GetAvatarV1;

use Src\services\token\Token;

use Src\services\name\ListNameV1;
use Src\services\address\ListAddressV1;
use Src\services\parent\ListParentV1;

use Src\services\name\DeleteNameV1;
use Src\services\address\DeleteAddressV1;
use Src\services\parent\DeleteParentV1;

use Src\services\parent\UpdateParentV1;

$isNotFound = false;
$route = new RouteHandler();

$route->route("GET", "/user/me", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new ListUserV1($authenticate[1]['id']);
    $userArray = json_decode(ListUserV1::$user, true);

    if(isset($userArray['name_id'])) {
        $name = new ListNameV1($userArray['name_id']);
        $nameArray = json_decode(ListNameV1::$name, true);
        unset($userArray['name_id']);
        $userArray["name"] = $nameArray;
    }
    
    if(isset($userArray['address_id'])) {
        $address = new ListAddressV1($userArray['address_id']);
        $addressArray = json_decode(ListAddressV1::$address, true);
        unset($userArray['address_id']);
        $userArray["address"] = $addressArray;
    }
    
    if(isset($userArray['parent_id'])) {
        $parent = new ListParentV1($userArray["parent_id"]);
        $parentArray = json_decode(ListParentV1::$parent, true);

        if(isset($parentArray["name_id"])) {
            $name = new ListNameV1($parentArray["name_id"]);
            $nameArray = json_decode(ListNameV1::$name);
        
            $parentArray["name"] = $nameArray;
            unset($parentArray["name_id"]);
        }

        unset($userArray['parent_id']);
        $userArray["parent"] = $parentArray;
    }

    echo json_encode($userArray);
});

$route->route("POST", "/user", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $user = new CreateUserV1(file_get_contents('php://input'));
    echo CreateUserV1::$lastInsertedUser;
}, auth: false);

$route->route("DELETE", "/user/me", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    new ListUserV1($authenticate[1]['id']);
    $userArray = json_decode(ListUserV1::$user, true);
    
    if(isset($userArray["name_id"])) {
        new UpdateUserV1($authenticate[1]['id'], '{"name_id": null}');
        new DeleteNameV1($userArray["name_id"]);
    }

    if(isset($userArray["address_id"])) {
        new UpdateUserV1($authenticate[1]['id'], '{"address_id": null}');
        new DeleteAddressV1($userArray["address_id"]);
    }

    if(isset($userArray["parent_id"])) {
        $parent = new ListParentV1($userArray["parent_id"]);
        $nameId = json_decode(ListParentV1::$parent)->name_id;

        new UpdateParentV1($userArray["parent_id"], '{"name_id": null}');
        new DeleteNameV1($nameId);

        new UpdateUserV1($authenticate[1]['id'], '{"parent_id": null}');
        new DeleteParentV1($userArray["parent_id"]); 
    }

    $token = new Token(userId: $authenticate[1]['id']);
    if($token->removedToken()) {
        new DeleteUserV1($authenticate[1]['id']);
    }

});

$route->route("PUT", "/user/me", function($authenticate){
    if(!$authenticate[0]) 
        exit();
        
    $user = new UpdateUserV1($authenticate[1]['id'], file_get_contents('php://input'));
    echo UpdateUserV1::$lastUpdatedUser;
});

$route->route("POST", "/user/avatar", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    $data = base64_encode(file_get_contents('php://input'));
    $user = new CreateAvatarV1($authenticate[1]['id'], $data);
});

$route->route("GET", "/user/avatar", function($authenticate){
    if(!$authenticate[0]) 
        exit();

    header("Content-Type: image/png");
    $user = new GetAvatarV1($authenticate[1]['id']);
    echo base64_decode(GetAvatarV1::$user);
});

$route->route("POST", "/user/login", function($is_authenticate){
    if(!$is_authenticate) 
        exit();
    
    $user = new LoginUserV1(file_get_contents('php://input'));
    if(!LoginUserV1::$isLogin) {
        header(HttpStatusCode::BAD_REQUEST);
        exit();
    }
    
    $auth = new UserAuth(LoginUserV1::$userLoginData);
    $token = $auth->get_token();
    
    $login = new Token(LoginUserV1::$userLoginData["id"]);
    $login->setToken($token);
    $token = $login->getToken();

    echo $token;
}, auth: false);

$route->route("POST", "/user/logout", function($authenticate){
    if(!$authenticate[0]) 
        exit();
    
    $addToken = new Token($authenticate[1]['id']);
    $is_logout = $addToken->removedToken();

    if($is_logout) {
        echo '{"success": "logout successfull"}';
    }

});

if(!($route->matchRoute() >= 1)) {
    $isNotFound = true;
}