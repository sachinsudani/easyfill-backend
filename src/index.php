<?php

    require __DIR__ . "/../config.php";
    require "vendor/autoload.php";

    use Src\services\user\CreateUserV1;
    use Src\services\user\UpdateUserV1;
    use Src\services\user\ListUserV1;
    use Src\services\user\DeleteUserV1;
    use Src\services\user\LoginUserV1;
    use Src\services\user\CreateAvatarV1;
    use Src\services\user\GetAvatarV1;
    
    use Src\services\name\CreateNameV1;
    use Src\services\name\DeleteNameV1;
    use Src\services\name\UpdateNameV1;
    use Src\services\name\ListNameV1;
    
    use Src\services\token\CreateTokenV1;
    use Src\services\token\DeleteTokenV1;
    
    use Src\services\parent\CreateParentV1;
    use Src\services\parent\DeleteParentV1;
    use Src\services\parent\UpdateParentV1;
    use Src\services\parent\ListParentV1;

    use Src\utils\RouteHandler;
    use Src\utils\UserAuth;
    use Src\HttpStatusCode;

    $route = new RouteHandler();

    
    $route->route("GET", "/user/[:id]", function($id, $is_authenticate){
        echo !$is_authenticate;
        if(!$is_authenticate) 
            exit();

        $user = new ListUserV1($id);
        echo ListUserV1::$user;
    });
    
    $route->route("POST", "/user", function($is_authenticate){
        if(!$is_authenticate) 
            exit();

        $user = new CreateUserV1(file_get_contents('php://input'));
        echo CreateUserV1::$lastInsertedUser;
    }, auth: false);
    
    $route->route("PUT", "/user/name/[:id]", function($id, $is_authenticate){
        if(!$is_authenticate) 
            exit();

        $user = new UpdateNameV1($id, file_get_contents('php://input'));
    }, auth: false);
    
    $route->route("DELETE", "/user/[:id]", function($is_authenticate){
        if(!$is_authenticate) 
            exit();
        
        $user = new DeleteUserV1($id);
        echo DeleteUserV1::$lastDeletedUser;
    });

    $route->route("DELETE", "/user/token/[:id]", function($id,$is_authenticate){
        if(!$is_authenticate) 
            exit();
        
        $user = new DeleteTokenV1($id);
        echo DeleteTokenV1::$name;
    }, auth: false);

    $route->route("DELETE", "/user/parent/[:id]", function($id, $is_authenticate){
        if(!$is_authenticate) 
            exit();
        
        $user = new DeleteParentV1($id);
        echo DeleteParentV1::$parent;
    }, auth: false);
    
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
        
        $addToken = new CreateTokenV1($token, LoginUserV1::$userLoginData["id"]);
        $token = $addToken->getToken();
        echo $token;
    }, auth: false);
    