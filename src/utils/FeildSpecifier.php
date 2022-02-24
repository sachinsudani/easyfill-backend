<?php

namespace src\utils;

class FeildSpecifier {
    public function __construct(
        public string $regex = "",
        public Bool $optional = false,
        public int $length = 0,
        public int $min = -1,
        public int $max = -1
    ) {}
}