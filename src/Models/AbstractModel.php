<?php

namespace FuncAI\Models;

use FuncAI\TensorFlow;

abstract class AbstractModel implements ModelInterface
{
    protected TensorFlow $tf;

    public function __construct()
    {
        $this->tf = new TensorFlow();
    }

    public function predict($input)
    {
        // Load the model
        $session = $this->tf->loadSavedModel($this->getModelPath());

        // Get the output tensor
        $output = $this->getOutputTensor();

        $inputTensor = $this->getInputTensor($input);

        $ret = $session->run(
            $output,
            [$this->getInputLayer() => $inputTensor],
        );

        return $this->transformResult($ret->value());
    }

    protected function transformResult($result)
    {
        return $result;
    }
}
