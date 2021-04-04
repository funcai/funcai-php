<?php

namespace FuncAI\Tensorflow;

use FFI;

class Operation
{
    public $c;
    private $graph;
    private static $operation_ptr;

    public function __construct(Graph $graph)
    {
        if(is_null(self::$operation_ptr)) {
            self::$operation_ptr = TensorFlow::$ffi->type("TF_Operation*");
        }
        $this->graph = $graph;
    }

    public function init($graph, $type, $name, array $input = [], array $control = [], array $attr = [], string $device = null)
    {
        $status = new Status();
        $desc = TensorFlow::$ffi->TF_NewOperation($graph->c, $type, $name);

        foreach ($input as $in) {
            if ($in instanceof Output) {
                TensorFlow::$ffi->TF_AddInput($desc, $in->c);
            } else if (is_array($in)) {
                $n_inputs = count($in);
                $c_inputs = TensorFlow::$ffi->new("TF_Output[$n_inputs]");
                $i = 0;
                foreach ($in as $el) {
                    $c_inputs[$i] = $el->c;
                    $i++;
                }
                TensorFlow::$ffi->TF_AddInputList($desc, $c_inputs, $n_inputs);
            }
        }

        foreach ($control as $ctl) {
            TensorFlow::$ffi->TF_AddControlInput($desc, $ctl->c);
        }

        foreach ($attr as $key => $val) {
            if (is_string($val)) {
                TensorFlow::$ffi->TF_SetAttrString($desc, $key, $val, strlen($val));
            } else if (is_int($val)) {
                TensorFlow::$ffi->TF_SetAttrInt($desc, $key, $val);
            } else if (is_float($val)) {
                TensorFlow::$ffi->TF_SetAttrFloat($desc, $key, $val);
            } else if (is_bool($val)) {
                TensorFlow::$ffi->TF_SetAttrBool($desc, $key, $val);
            } else if (is_object($val) && $val instanceof Type) {
                TensorFlow::$ffi->TF_SetAttrType($desc, $key, $val->type);
            } else if (is_object($val) && $val instanceof FuncName) {
                TensorFlow::$ffi->TF_SetAttrFuncName($desc, $key, $val->func_name, strlen($val->func_name));
            } else if (is_object($val) && $val instanceof Shape) {
                $shape = $val->shape;
                $num_dims = count($shape);
                $dims = TensorFlow::$ffi->new("int64_t[$num_dims]");
                $j = 0;
                foreach ($shape as $dim) {
                    $dims[$j++] = (int)$dim;
                }
                TensorFlow::$ffi->TF_SetAttrShape($desc, $key, $dims, $num_dims);
            } else if (is_object($val) && $val instanceof Tensor) {
                TensorFlow::$ffi->TF_SetAttrTensor($desc, $key, $val->c, $status->c);
                if ($status->code() != TensorFlow::OK) {
                    throw new TensorflowException($status->error());
                }
            } else if (is_array($val) && count($val) > 0) {
                $num = count($val);
                foreach ($val as $el) break;
                if (is_string($el)) {
                    $buf = TensorFlow::$ffi->new("char*[$num]");
                    $len = TensorFlow::$ffi->new("size_t[$num]");
                    $i = 0;
                    foreach ($val as $el) {
                        if (is_string($el)) {
                            $buf[$i] = $el; //???
                            $len[$i] = strlen($el);
                            $i++;
                        } else {
                            throw new TensorflowException("Wrong attr type");
                        }
                    }
                    TensorFlow::$ffi->TF_SetAttrStringList($desc, $key, $buf, $len, $num);
                } else if (is_int($el)) {
                    $buf = TensorFlow::$ffi->new("int64_t[$num]");
                    $i = 0;
                    foreach ($val as $el) {
                        if (is_int($el)) {
                            $buf[$i++] = $el;
                        } else {
                            throw new TensorflowException("Wrong attr type");
                        }
                    }
                    TensorFlow::$ffi->TF_SetAttrIntList($desc, $key, $buf, $num);
                } else if (is_float($el)) {
                    $buf = TensorFlow::$ffi->new("float[$num]");
                    $i = 0;
                    foreach ($val as $el) {
                        if (is_float($el)) {
                            $buf[$i++] = $el;
                        } else {
                            throw new TensorflowException("Wrong attr type");
                        }
                    }
                    TensorFlow::$ffi->TF_SetAttrFloatList($desc, $key, $buf, $num);
                } else if (is_bool($el)) {
                    $buf = TensorFlow::$ffi->new("unsigned char[$num]");
                    $i = 0;
                    foreach ($val as $el) {
                        if (is_bool($el)) {
                            $buf[$i++] = $el;
                        } else {
                            throw new TensorflowException("Wrong attr type");
                        }
                    }
                    TensorFlow::$ffi->TF_SetAttrBoolList($desc, $key, $buf, $num);
                } else if (is_object($el) && $el instanceof Type) {
                    $buf = TensorFlow::$ffi->new("TF_DataType[$num]");
                    $i = 0;
                    foreach ($val as $el) {
                        if ($el instanceof Type) {
                            $buf[$i++] = $el->type;
                        } else {
                            throw new TensorflowException("Wrong attr type");
                        }
                    }
                    TensorFlow::$ffi->TF_SetAttrTypeList($desc, $key, $buf, $num);
                } else if (is_object($el) && $el instanceof Shape) {
                    $buf = TensorFlow::$ffi->new("int64_t*[$num]");
                    $len = TensorFlow::$ffi->new("int[$num]");
                    $i = 0;
                    foreach ($val as $el) {
                        if ($el instanceof Shape) {
                            $shape = $el->shape;
                            $num_dims = count($shape);
                            $dims = TensorFlow::$ffi->new("int64_t[$num_dims]");
                            $j = 0;
                            foreach ($shape as $dim) {
                                $dims[$j++] = (int)$dim;
                            }
                            $buf[$i] = $dims;
                            $len[$i] = $num_dims;
                            $i++;
                        } else {
                            throw new TensorflowException("Wrong attr type");
                        }
                    }
                    TensorFlow::$ffi->TF_SetAttrShapeList($desc, $key, $buf, $len, $num);
                } else if (is_object($el) && $el instanceof Tensor) {
                    $buf = TensorFlow::$ffi->new("TF_Tensor*[$num]");
                    $i = 0;
                    foreach ($val as $el) {
                        if ($el instanceof Tensor) {
                            $buf[$i++] = $el->type;
                        } else {
                            throw new TensorflowException("Wrong attr type");
                        }
                    }
                    TensorFlow::$ffi->TF_SetAttrTensorList($desc, $key, $buf, $num, $status->c);
                    if ($status->code() != TensorFlow::OK) {
                        throw new TensorflowException($status->error());
                    }
                } else {
                    throw new TensorflowException("Unknown Operation attr type");
                }
            } else {
                throw new TensorflowException("Unknown Operation attr type");
            }
        }

        if (is_string($device)) {
            TensorFlow::$ffi->TF_SetDevice($desc, $device);
        } else if (!is_null($device)) {
            throw new TensorflowException("Wrong Operation device");
        }

        $this->c = TensorFlow::$ffi->TF_FinishOperation($desc, $status->c);
        if ($status->code() != TensorFlow::OK) {
            throw new TensorflowException($status->error());
        }
    }

    public function name()
    {
        return (string)TensorFlow::$ffi->TF_OperationName($this->c);
    }

    public function type()
    {
        return (string)TensorFlow::$ffi->TF_OperationOpType($this->c);
    }

    public function device()
    {
        return (string)TensorFlow::$ffi->TF_OperationDevice($this->c);
    }

    public function numInputs()
    {
        return (int)TensorFlow::$ffi->TF_OperationNumInputs($this->c);
    }

    public function numOutputs()
    {
        return (int)TensorFlow::$ffi->TF_OperationNumOutputs($this->c);
    }

    public function inputListSize($name)
    {
        $status = new Status();
        $ret = (int)TensorFlow::$ffi->TF_OperationInputListLength($this->c, $name, $status->c);
        if ($status->code() != TensorFlow::OK) {
            throw new TensorflowException($status->error());
        }
        return $ret;
    }

    public function outputListSize($name)
    {
        $status = new Status();
        $ret = (int)TensorFlow::$ffi->TF_OperationOutputListLength($this->c, $name, $status->c);
        if ($status->code() != TensorFlow::OK) {
            throw new TensorflowException($status->error());
        }
        return $ret;
    }

    public function input($n)
    {
        $input = new Input($this->graph);
        $input->init($this, $n);
        return $input;
    }

    public function output($n)
    {
        $output = new Output($this->graph);
        $output->init($this, $n);
        return $output;
    }

    public function controlInputs()
    {
        $num = $this->numControlInputs();
        if ($num) {
            $type = FFI::arrayType(self::$operation_ptr, [$num]);
            $buf = TensorFlow::$ffi->new($type);
            $num = TensorFlow::$ffi->TF_OperationGetControlInputs($this->c, $buf, $num);
            if ($num) {
                $ret = [];
                for ($i = 0; $i < $num; $i++) {
                    $in = new Operation($this->graph);
                    $in->initFromC(clone $buf[$i]);
                    $ret[] = $in;
                }
                return $ret;
            }
        }
        return [];
    }

    public function numControlInputs()
    {
        return (int)TensorFlow::$ffi->TF_OperationNumControlInputs($this->c);
    }

    public function initFromC($cdata)
    {
        $this->c = $cdata;
    }

    public function controlOutputs()
    {
        $num = $this->numControlOutputs();
        if ($num) {
            $type = FFI::arrayType(self::$operation_ptr, [$num]);
            $buf = TensorFlow::$ffi->new($type);
            $num = TensorFlow::$ffi->TF_OperationGetControlOutputs($this->c, $buf, $num);
            if ($num) {
                $ret = [];
                for ($i = 0; $i < $num; $i++) {
                    $in = new Operation($this->graph);
                    $in->initFromC(clone $buf[$i]);
                    $ret[] = $in;
                }
                return $ret;
            }
        }
        return [];
    }

    public function numControlOutputs()
    {
        return (int)TensorFlow::$ffi->TF_OperationNumControlOutputs($this->c);
    }

}
