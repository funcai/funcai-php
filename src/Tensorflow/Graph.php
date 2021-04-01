<?php

namespace FuncAI\Tensorflow;

class Graph
{
    public $c;
    private $nameNum = [];

    public function __construct()
    {
        $this->c = TensorFlow::$ffi->TF_NewGraph();
    }

    public function __destruct()
    {
        TensorFlow::$ffi->TF_DeleteGraph($this->c);
    }

    public function operation(string $name)
    {
        $cdata = TensorFlow::$ffi->TF_GraphOperationByName($this->c, $name);
        if (is_null($cdata)) {
            return null;
        }
        $op = new Operation($this);
        $op->initFromC($cdata);
        return $op;
    }

    public function operations()
    {
        $pos = TensorFlow::$ffi->new("size_t[1]");
        $pos[0] = 0;
        $ops = [];
        while (1) {
            $cdata = TensorFlow::$ffi->TF_GraphNextOperation($this->c, $pos);
            if (is_null($cdata)) {
                break;
            }
            $op = new Operation($this);
            $op->initFromC($cdata);
            $ops[] = $op;
        }
        return $ops;
    }

    public function addOperation($type, $name, array $input = [], array $control = [], array $attr = [])
    {
        if (is_null($name)) {
            $name = $this->_genName($type);
        } else if (!is_null(TensorFlow::$ffi->TF_GraphOperationByName($this->c, $name))) {
            $name = $this->_genName($name);
        }
        $op = new Operation($this);
        $op->init($this, $type, $name, $input, $control, $attr);
        return $op;
    }

    private function _genName($name)
    {
        if (isset($this->nameNum[$name])) {
            $num = ++$this->nameNum[$name];
        } else {
            $this->nameNum[$name] = 1;
            $num = 1;
        }
        return $name . "_" . $num;
    }

    public function export()
    {
        $status = new Status();
        $buf = new Buffer();
        TensorFlow::$ffi->TF_GraphToGraphDef($this->c, $buf->c, $status->c);
        if ($status->code() != TensorFlow::OK) {
            throw new TensorflowException($status->error());
        }
        return $buf->string();
    }

    public function import(string $def, string $prefix = "")
    {
        $opts = new ImportGraphDefOptions();
        $opts->setPrefix($prefix);
        $buf = new Buffer($def);
        $status = new Status();
        TensorFlow::$ffi->TF_GraphImportGraphDef($this->c, $buf->c, $opts->c, $status->c);
        if ($status->code() != TensorFlow::OK) {
            throw new TensorflowException($status->error());
        }
    }
}
