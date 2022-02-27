<?php

namespace src\utils;
use Src\utils\UserAuth;

class RouteHandler {
    private $requestMethod;
    public $route;
    private static $authRoute = array();
    
    public function __construct() {
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->route = "/" . explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 3)[2];
    }
    
    public function route($requestMethod, $route, $callback, $auth = true) {
        if($auth) {
            array_push(RouteHandler::$authRoute, $route); 
        }

        if($requestMethod == $this->requestMethod) {
            if($this->route == $route) {
                $callback(self::authenticate($route, $auth));
            }

            if(str_contains($route, "[:id]")) {
                $routeArray = explode("/", $route);
                $requestRouteArray = explode("/", $this->route);
                $routeId = (int) $requestRouteArray[count($requestRouteArray) - 1];

                array_pop($routeArray);
                array_push($routeArray, $routeId);

                $actualRoute = implode("/",$routeArray);
                if(strcmp($this->route, $actualRoute) == 0) {
                    $callback($routeId,self::authenticate($route, $auth));
                }
            }
        }
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