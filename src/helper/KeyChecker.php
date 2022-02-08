<?php

namespace src\helper;
use Src\HttpStatusCode;

class KeyChecker {
    public static function validate($keys, $array) {
        for($item = 0 ; $item < count($keys) ; $item ++) {
            if(!str_ends_with($keys[$item], '?')) {
                if(!array_key_exists($keys[$item], $array)) {
                    header(HttpStatusCode::BAD_REQUEST);
                    exit();
                }
            }
        }

        return true;
    }
}