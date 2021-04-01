<?php
// The tensorflow classes are inspired by: https://github.com/dstogov/php-tensorflow

namespace FuncAI\Tensorflow;

class Status
{
    public $c;

    public function __construct()
    {
        $this->c = TensorFlow::$ffi->TF_NewStatus();
    }

    public function __destruct()
    {
        TensorFlow::$ffi->TF_DeleteStatus($this->c);
    }

    public function code()
    {
        return (int)TensorFlow::$ffi->TF_GetCode($this->c);
    }

    public function error()
    {
        return $this->string();
    }

    public function string()
    {
        return (string)TensorFlow::$ffi->TF_Message($this->c);
    }
}
