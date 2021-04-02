<?php
// The tensorflow classes are inspired by: https://github.com/dstogov/php-tensorflow
namespace FuncAI\Tensorflow;

use Exception;
use FFI;

class TensorFlow
{
    public const OK = 0;
    public const CANCELLED = 1;
    public const UNKNOWN = 2;
    public const INVALID_ARGUMENT = 3;
    public const DEADLINE_EXCEEDED = 4;
    public const NOT_FOUND = 5;
    public const ALREADY_EXISTS = 6;
    public const PERMISSION_DENIED = 7;
    public const UNAUTHENTICATED = 16;
    public const RESOURCE_EXHAUSTED = 8;
    public const FAILED_PRECONDITION = 9;
    public const ABORTED = 10;
    public const OUT_OF_RANGE = 11;
    public const UNIMPLEMENTED = 12;
    public const INTERNAL = 13;
    public const UNAVAILABLE = 14;
    public const DATA_LOSS = 15;

    public const FLOAT = 1;
    public const DOUBLE = 2;
    public const INT32 = 3;
    public const UINT8 = 4;
    public const INT16 = 5;
    public const INT8 = 6;
    public const STRING = 7;
    public const COMPLEX64 = 8;
    public const COMPLEX = 8;
    public const INT64 = 9;
    public const BOOL = 10;
    public const QINT8 = 11;
    public const QUINT8 = 12;
    public const QINT32 = 13;
    public const BFLOAT16 = 14;
    public const QINT16 = 15;
    public const QUINT16 = 16;
    public const UINT16 = 17;
    public const COMPLEX128 = 18;
    public const HALF = 19;
    public const RESOURCE = 20;
    public const VARIANT = 21;
    public const UINT32 = 22;
    public const UINT64 = 23;

    public static $ffi;
    private $graph;
    private $status;

    public function __construct()
    {
        if (is_null(TensorFlow::$ffi)) {
            $this->initializeFFI();
        }
    }

    private function initializeFFI()
    {
        $cwd = getcwd();
        chdir(realpath(__DIR__ . '/../../lib'));

        TensorFlow::$ffi = FFI::load(__DIR__ . "/../../c/tf_singlefile.2.3.0.h");

        chdir($cwd);
    }

    public function version()
    {
        return (string)TensorFlow::$ffi->TF_Version();
    }

    public function getDefaultGraph()
    {
        if (!isset($this->graph)) {
            $this->graph = new Graph();
        }
        return $this->graph;
    }

    public function loadSavedModel(string $dir, array $tags = ["serve"], SessionOptions $options = null)
    {
        if (is_null($options)) {
            $options = new SessionOptions();
        }
        $n_tags = count($tags);
        $c_tags = TensorFlow::$ffi->new("char*[$n_tags]");
        $i = 0;
        foreach ($tags as $tag) {
            $len = strlen($tag);
            $c_len = $len + 1;
            $str = TensorFlow::$ffi->new("char[$c_len]", false);
            FFI::memcpy($str, $tag, $len);
            $c_tags[$i] = $str;
            $i++;
        }
        $graph = $this->getDefaultGraph();
        $status = $this->getDefaultStatus();
        $c_session = TensorFlow::$ffi->TF_LoadSessionFromSavedModel(
            $options->c,
            null, // const TF_Buffer* run_options,
            $dir,
            $c_tags,
            $n_tags,
            $graph->c,
            null, // TF_Buffer* meta_graph_def,
            $status->c);
        for ($i = 0; $i < $n_tags; $i++) {
            FFI::free($c_tags[$i]);
        }
        if ($status->code() != self::OK) {
            throw new Exception($status->error());
        }
        return new Session($graph, $options, $status, $c_session);
    }

    protected function getDefaultStatus()
    {
        if (!isset($this->status)) {
            $this->status = new Status();
        }
        return $this->status;
    }

    public function tensor($value, $dataType = null, $shape = null)
    {
        $status = $this->getDefaultStatus();
        $tensor = new Tensor();
        $tensor->init($value, $dataType, $shape, $status);
        return $tensor;
    }

    public function constant($value, $dataType = null, $shape = null, $name = null)
    {
        $status = $this->getDefaultStatus();
        $tensor = new Tensor();
        $tensor->init($value, $dataType, $shape, $status);
        return $this->op("Const", [], [], [
            "dtype" => new Type($tensor->type()),
            "value" => $tensor,
        ], $name);
    }

    public function op($type, array $input = [], array $control = [], array $attr = [], $name = null, $n = 0)
    {
        $graph = $this->getDefaultGraph();
        $op = $graph->addOperation($type, $name, $input, $control, $attr);
        return $op->output($n);
    }

    public function session()
    {
        $graph = $this->getDefaultGraph();
        $status = $this->getDefaultStatus();
        return new Session($graph, null, $this->status);
    }
}