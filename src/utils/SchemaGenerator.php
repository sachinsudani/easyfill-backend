<?php

namespace src\utils;
use Src\utils\FeildSpecifier;

class SchemaGenerator {
    public function __construct(public array $feilds)
    {
        foreach($feilds as $key => $val) {
            $this->feilds[$key] = new FeildSpecifier(...$val);
        }
    }
}