<?php

namespace src\utils;
use Src\utils\UserAuth;

class RouteHandler {
    private $requestMethod;
    public $route;
    private static $routeCount = 0;
    private static $authRoute = array();
    
    public function __construct() {
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->route = "/" . explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 2)[1];
    }
    
    public function route($requestMethod, $route, $callback, $auth = true) {
        if($auth) {
            array_push(RouteHandler::$authRoute, $route); 
        }

        if($requestMethod == $this->requestMethod) {
            if($this->route == $route) {
                $callback(self::authenticate($route, $auth));
                RouteHandler::$routeCount ++;
            }
        }
    }

    public function matchRoute() {
        return RouteHandler::$routeCount;
    }

    public function authenticate($route, $auth) {
        if(!$auth) {
            $result = array(true);
            return $result;
        }
        
        if(in_array($route, RouteHandler::$authRoute)) {
            new UserAuth();
            $result = UserAuth::validateJWT();
            return $result;
        }
    }
}