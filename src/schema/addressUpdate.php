<?php

$addressUpdateSchema = array(
    "street_line_1" => array("length" => 100, "optional" => true),
    "area" => array("length" => 100, "optional" => true),
    "locality" => array("length" => 100, "optional" => true),
    "house_no" => array("length" => 100, "optional" => true),
    "post_office" => array("length" => 100, "optional" => true),
    "state" => array("length" => 100, "optional" => true),
    "district" => array("length" => 100, "optional" => true),
    "sub_district" => array("length" => 100, "optional" => true),
    "city" => array("length" => 100, "optional" => true),
    "pincode" => array("length" => 6, "regex" => "/^[0-9]{6}$/","optional" => true)
);
