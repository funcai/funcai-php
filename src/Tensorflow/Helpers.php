<?php
// The tensorflow classes are inspired by: https://github.com/dstogov/php-tensorflow

namespace FuncAI\Tensorflow;

class Helpers {
    const TYPE_NAME = [
        TensorFlow::FLOAT => "FLOAT",
        TensorFlow::DOUBLE => "DOUBLE",
        TensorFlow::INT32 => "INT32",
        TensorFlow::UINT8 => "UINT8",
        TensorFlow::INT16 => "INT16",
        TensorFlow::INT8 => "INT8",
        TensorFlow::STRING => "STRING",
        TensorFlow::COMPLEX64 => "COMPLEX64",
        TensorFlow::COMPLEX => "COMPLEX",
        TensorFlow::INT64 => "INT64",
        TensorFlow::BOOL => "BOOL",
        TensorFlow::QINT8 => "QINT8",
        TensorFlow::QUINT8 => "QUINT8",
        TensorFlow::QINT32 => "QINT32",
        TensorFlow::BFLOAT16 => "BFLOAT16",
        TensorFlow::QINT16 => "QINT16",
        TensorFlow::QUINT16 => "QUINT16",
        TensorFlow::UINT16 => "UINT16",
        TensorFlow::COMPLEX128 => "COMPLEX128",
        TensorFlow::HALF => "HALF",
        TensorFlow::RESOURCE => "RESOURCE",
        TensorFlow::VARIANT => "VARIANT",
        TensorFlow::UINT32 => "UINT32",
        TensorFlow::UINT64 => "UINT64",
    ];

    public static function getTypeName($type, $shape)
    {
        if ($type < 100) {
            $name = self::TYPE_NAME[$type];
        } else {
            $name = '&' . self::TYPE_NAME[$type - 100];
        }
        if (is_array($shape) && count($shape) > 0) {
            $name .= '[' . implode(',', $shape) . ']';
        }
        return $name;
    }

    public static function printGraph(Graph $g)
    {
        foreach ($g->operations() as $op) {
            echo $op->name() . ": " . $op->type() . ", " . $op->device() . "\n";
            $count = $op->numInputs();
            for ($i = 0; $i < $count; $i++) {
                $in = $op->input($i);
                $out = $in->producer();
                echo "  in_$i: " . $in->typeName($i) .
                    ", from " . $out->op()->name() . "/out_" . $out->index() . "\n";
            }
            $count = $op->numOutputs();
            for ($i = 0; $i < $count; $i++) {
                $out = $op->output($i);
                $num = $out->numConsumers();
                $s = "";
                if ($num) {
                    $inputs = $out->consumers();
                    $s = ", to (";
                    $first = true;
                    foreach ($inputs as $in) {
                        if (!$first) {
                            $s .= ", ";
                        } else {
                            $first = false;
                        }
                        $s .= $in->op()->name() . "/" . $in->index();
                    }
                    $s .= ")";
                }
                echo "  out_$i: " . $out->typeName($i) . "$s\n";
            }
            $i = 0;
            foreach ($op->controlInputs() as $ctl) {
                $i++;
                echo "  ctl_in_$i: " . $ctl->name() . "\n";
            }
            $i = 0;
            foreach ($op->controlOutputs() as $ctl) {
                $i++;
                echo "  ctl_out_$i: " . $ctl->name() . "\n";
            }
        }
        echo "\n";
    }
}
