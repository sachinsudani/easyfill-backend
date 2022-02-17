<?php

$userSchema = array(
    "username" => array("length" => 100),
    "password" => array("length" => 100),
    "email" => array(
        "length" => 255, 
        "regex" => "/^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$/"
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
    )
);