<?php

namespace src\helper;
use Src\Helper\FeildSpecifier;

class SchemaGenerator {
    public function __construct(public array $feilds)
    {
        foreach($feilds as $key => $val) {
            $this->feilds[$key] = new FeildSpecifier(...$val);
        }
    }
}