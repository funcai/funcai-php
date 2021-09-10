<?php

// The tensorflow classes are inspired by: https://github.com/dstogov/php-tensorflow

namespace FuncAI\Tensorflow;

class FuncName
{
    public $func_name;

    public function __construct(string $func_name)
    {
        $this->shape_proto = $func_name;
    }
}
