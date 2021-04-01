<?php
// The tensorflow classes are inspired by: https://github.com/dstogov/php-tensorflow

namespace FuncAI\Tensorflow;

class Shape
{
    public $shape;

    function __construct(array $shape = null)
    {
        $this->shape = $shape;
    }
}
