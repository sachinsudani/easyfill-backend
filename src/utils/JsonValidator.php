<?php

namespace src\utils;
use Src\HttpStatusCode;

class JsonValidator {

    /**
     * @method(validate())
     *      To validate the json comes from the request and gives the 400 error if the json 
     * is not proper (returns Boolean).
     */

    public static function validate($jsonArray, $schema) {
        foreach($schema->feilds as $feild => $feildSpecifier){
 
            if($feildSpecifier->optional && !isset($jsonArray[$feild])) continue;

            if(!$feildSpecifier->optional && !isset($jsonArray[$feild])) {
                echo '{"error": "'. $feild.' is required."}';
                header(HttpStatusCode::BAD_REQUEST);
                exit();
            }
            
            if($feildSpecifier->regex != "") {
                if($feild == "email") {
                    if(!filter_var($jsonArray[$feild], FILTER_VALIDATE_EMAIL)) {
                        echo '{"error": "'. $feild .' is Invalid."}';
                        header(HttpStatusCode::BAD_REQUEST);
                        exit();
                    }
                } else {
                    if(!preg_match($feildSpecifier->regex, $jsonArray[$feild])) {
                        echo '{"error": "'. $feild .' is Invalid."}';
                        header(HttpStatusCode::BAD_REQUEST);
                        exit();
                    }
                }
            }
            
            if($feildSpecifier->length != 0) {
                if(strlen($jsonArray[$feild]) > $feildSpecifier->length){
                    echo '{"error": "'. $feild .' is too long."}';
                    header(HttpStatusCode::BAD_REQUEST);
                    exit();
                }
            }
            
            if($feildSpecifier->max != -1 && $feildSpecifier->min != -1) {
                if(!(strlen($jsonArray[$feild]) >= $feildSpecifier->min && strlen($jsonArray[$feild]) <= $feildSpecifier->max)) {
                    echo '{"error": "'. $feild .' is not valid."}';
                    header(HttpStatusCode::BAD_REQUEST);
                    exit();
                }
            }

        }
        return true;
    }

    /**
     * @method(pretify())
     *      To clear all the white spaces from the json string and remove un-neccesay feilds
     * from the reterive json (returns Array).  
     */

    public static function pretify($json, $schema, $is_strict = false) {
        $jsonArray = json_decode($json, true);
        $pretifyJsonArray = array();
        $feilds = array();

        foreach($schema->feilds as $key => $val) {
            array_push($feilds, $key);
        }

        foreach($jsonArray as $key => $val) {
            if(in_array($key, $feilds)) {
                trim($val, " ");
                $pretifyJsonArray[$key] = $val;
            } 
            else {
                if($is_strict) {
                    header(HttpStatusCode::BAD_REQUEST);
                    exit();
                }
            }
        }
        return $pretifyJsonArray;
    }
}