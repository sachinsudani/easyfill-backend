<?php

use Src\HttpStatusCode;

require_once __DIR__ . "/user.php";
require_once __DIR__ . "/name.php";
require_once __DIR__ . "/parent.php";
require_once __DIR__ . "/address.php";

if($isNotFound) {
    header(HttpStatusCode::NOT_FOUND);
    exit();
}