<?php

namespace FuncAI\Tensorflow;

use FFI;
use FFI\CData;
use FuncAI\API;

class Buffer
{
    public $c;

    public function __construct($str = null)
    {
        if (is_null($str)) {
            $this->c = TensorFlow::$ffi->TF_NewBuffer();
        } elseif (is_object($str) &&
            $str instanceof CData &&
            TensorFlow::$ffi->type($str) == TensorFlow::$ffi->type('TF_Buffer*')) {
            $this->c = $str;
        } else {
            $this->c = TensorFlow::$ffi->TF_NewBufferFromString($str, strlen($str));
        }
    }

    public function __destruct()
    {
        TensorFlow::$ffi->TF_DeleteBuffer($this->c);
    }

    public function string()
    {
        return FFI::string($this->c[0]->data, $this->c[0]->length);
    }
}
