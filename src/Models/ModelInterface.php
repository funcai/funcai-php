<?php

namespace FuncAI\Models;

use FuncAI\Tensorflow\Output;
use FuncAI\Tensorflow\Tensor;

interface ModelInterface
{
    public function getModelPath(): string;
    public function getOutputTensor(): Output;
    /**
     * @param mixed $input
     *
     * @return Tensor|array<string, Tensor>
     */
    public function getInputData($input);
    public function getInputLayer(): string;
}
