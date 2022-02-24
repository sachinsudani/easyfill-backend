<?php

require __DIR__ . "/../config.php";
require "vendor/autoload.php";

use Src\services\user\CreateUserV1;
use Src\services\user\UpdateUserV1;
use Src\services\user\ListUserV1;
use Src\services\user\DeleteUserV1;
use Src\services\user\LoginUserV1;

use Src\services\address\CreateAddressV1;

use Src\services\token\CreateTokenV1;

use Src\utils\RouteHandler;
use Src\utils\UserAuth;
use Src\HttpStatusCode;

$route = new RouteHandler();

$route->route("GET", "/user/[:id]", function ($id, $is_authenticate) {
    echo !$is_authenticate;
    if (!$is_authenticate)
        exit();

    $user = new ListUserV1($id);
    echo ListUserV1::$user;
});

$route->route("POST", "/user", function ($is_authenticate) {
    if (!$is_authenticate)
        exit();

    $user = new CreateUserV1(file_get_contents('php://input'));
    echo CreateUserV1::$lastInsertedUser;
}, auth: false);

$route->route("PUT", "/user/[:id]", function ($id, $is_authenticate) {
    if (!$is_authenticate)
        exit();

    $user = new UpdateUserV1($id, file_get_contents('php://input'));
    echo UpdateUserV1::$lastUpdatedUser;
});

$route->route("DELETE", "/user/[:id]", function ($is_authenticate) {
    if (!$is_authenticate)
        exit();

    $user = new DeleteUserV1($id);
    echo DeleteUserV1::$lastDeletedUser;
});

//routes for address start

$route->route("POST", "/address", function ($is_authenticate) {
    if (!$is_authenticate)
        exit();

    $address = new CreateAddressV1(file_get_contents('php://input'));
    echo CreateAddressV1::$lastInsertedAddress;
}, auth: false);

//routes for address end

$route->route("POST", "/user/login", function ($is_authenticate) {
    if (!$is_authenticate)
        exit();

    $user = new LoginUserV1(file_get_contents('php://input'));
    if (!LoginUserV1::$isLogin) {
        header(HttpStatusCode::BAD_REQUEST);
        exit();
    }

    $auth = new UserAuth(LoginUserV1::$userLoginData);
    $token = $auth->get_token();

    $addToken = new CreateTokenV1($token, LoginUserV1::$userLoginData["id"]);
    $token = $addToken->getToken();
    echo $token;
}, auth: false);
