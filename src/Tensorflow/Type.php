<?php

// The tensorflow classes are inspired by: https://github.com/dstogov/php-tensorflow

namespace FuncAI\Tensorflow;

class Type
{
    public $type;

    public function __construct(int $type)
    {
        $this->type = $type;
    }
}
