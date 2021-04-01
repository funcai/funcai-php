<?php

namespace FuncAI\Tensorflow;

class ImportGraphDefOptions
{
    public $c;

    public function __construct()
    {
        $this->c = TensorFlow::$ffi->TF_NewImportGraphDefOptions();
    }

    public function __destruct()
    {
        TensorFlow::$ffi->TF_DeleteImportGraphDefOptions($this->c);
    }

    public function setPrefix(string $prefix)
    {
        TensorFlow::$ffi->TF_ImportGraphDefOptionsSetPrefix($this->c, $prefix);
    }
}
