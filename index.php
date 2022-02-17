<?php

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $request_uri);

if(count($uri) >= 3) {
    switch($uri[2]){
        case "user":
            echo "user";
            break;

        default:
            echo "abc";
            break;
    }
}