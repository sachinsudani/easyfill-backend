<?php

$userUpdateSchema = array(
    "username" => array(
        "length" => 100,
        "optional" => true
    ),
    "password" => array(
        "length" => 100,
        "optional" => true
    ),
    "email" => array(
        "length" => 255, 
        "regex" => "[Email Regex]",
        "optional" => true
    ),
    "contact_no" => array(
        "length" => 10, 
        "regex" => "/^[0-9]{10}$/", 
        "min" => 10, 
        "max" => 10, 
        "optional" => true
    ),
    "dob" => array(
        "regex" => "/^(19|20)\d\d[\/.](0[1-9]|[12][0-9]|3[01])[\/.](0[1-9]|1[012])$/",
        "length" => 11, 
        "optional" => true
    ),
    "gender" => array(
        "length" => 6,
        "optional" => true
    ),
    "address_id" => array(
        "length" => 3,
        "optional" => true
    ),
    "name_id" => array(
        "length" => 3,
        "optional" => true
    ),
    "parent_id" => array(
        "length" => 3,
        "optional" => true
    )
);