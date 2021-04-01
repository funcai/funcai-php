<?php

namespace FuncAI\Tensorflow;

class SessionOptions
{
    public $c;

    public function __construct()
    {
        $this->c = TensorFlow::$ffi->TF_NewSessionOptions();
    }

    public static function setTarget()
    {
        throw new TensorflowException("Not Implemented"); //???
    }

    public static function setConfig()
    {
        throw new TensorflowException("Not Implemented"); //???
    }

    public function __destruct()
    {
        TensorFlow::$ffi->TF_DeleteSessionOptions($this->c);
    }
}
