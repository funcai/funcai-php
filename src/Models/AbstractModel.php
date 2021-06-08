<?php

namespace FuncAI\Models;

use FuncAI\Tensorflow\Helpers;
use FuncAI\Tensorflow\TensorFlow;
use FuncAI\TensorFlow\TensorflowException;

abstract class AbstractModel implements ModelInterface
{
    protected TensorFlow $tf;
    private $session;

    protected static $tensorflow;
    protected static $_models;

    public function __construct()
    {
        if (!extension_loaded("FFI")) {
            throw new TensorflowException("FFI extension required");
        }
        $this->tf = $this->getTensorflow();
    }

    private function getTensorflow() {
        if(!self::$tensorflow) {
            self::$tensorflow = new TensorFlow();
        }
        return self::$tensorflow;
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

        $inputData = $this->getInputData($input);
        if(!is_array($inputData)) {
            $inputData = [$this->getInputLayer() => $inputData];
        }

        $ret = $session->run(
            $output,
            $inputData,
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
        if(!self::$_models[$this->getModelPath()]) {
            return;
        }
        $this->getSession()->close();
    }

    public function printGraph()
    {
        Helpers::printGraph($this->getSession()->getGraph());
    }

    protected function getSession()
    {
        $modelPath = $this->getModelPath();
        if(!isset(self::$_models[$modelPath])) {
            self::$_models[$modelPath] = $this->tf->loadSavedModel($this->getModelPath());
        }
        return self::$_models[$modelPath];
    }

    protected function transformResult($result)
    {
        return $result;
    }

    public function getInputLayer()
    {
        return '';
    }
}
