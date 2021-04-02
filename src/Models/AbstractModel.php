<?php

namespace FuncAI\Models;

use FuncAI\Tensorflow\TensorFlow;
use FuncAI\TensorFlow\TensorflowException;

abstract class AbstractModel implements ModelInterface
{
    protected TensorFlow $tf;
    private $session;

    public function __construct()
    {
        if (!extension_loaded("FFI")) {
            throw new TensorflowException("FFI extension required");
        }
        $this->tf = new TensorFlow();
    }

    /**
     * Preloads the model into memory
     * Do this for example after you've started your queue worker
     */
    public function boot()
    {
        $this->getSession();
    }

    /**
     * Runs the model to return the predicted output
     *
     * @param $input
     * @return mixed
     * @throws TensorflowException
     */
    public function predict($input)
    {
        // Load the model
        $session = $this->getSession();

        // Get the output tensor
        $output = $this->getOutputTensor();

        $inputTensor = $this->getInputTensor($input);

        $ret = $session->run(
            $output,
            [$this->getInputLayer() => $inputTensor],
        );

        return $this->transformResult($ret->value());
    }

    /**
     * Cleanup memory
     *
     * @throws TensorflowException
     */
    public function close()
    {
        if(!$this->session) {
            return;
        }
        $this->getSession()->close();
    }

    protected function getSession()
    {
        if(is_null($this->session)) {
            $this->session = $this->tf->loadSavedModel($this->getModelPath());
        }
        return $this->session;
    }

    protected function transformResult($result)
    {
        return $result;
    }
}
