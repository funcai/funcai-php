<?php

namespace FuncAI\Models;

use FuncAI\Tensorflow\Output;
use FuncAI\Tensorflow\Tensor;

interface ModelInterface {
    function getModelPath(): string;
    function getOutputTensor(): Output;
    /**
     * @param mixed $input
     *
     * @return Tensor|array<string, Tensor>
     */
    function getInputData($input);
    function getInputLayer(): string;
}
