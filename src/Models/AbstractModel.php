<?php

namespace FuncAI\Models;

use FuncAI\Tensorflow\Helpers;
use FuncAI\Tensorflow\Session;
use FuncAI\Tensorflow\TensorFlow;
use FuncAI\Tensorflow\TensorflowException;

abstract class AbstractModel implements ModelInterface
{
    protected TensorFlow $tf;

    protected TensorFlow $tensorflow;
    
    /** @var array<string, Session> */
    protected static array $_models;

    public function __construct()
    {
        if (!extension_loaded("FFI")) {
            throw new TensorflowException("FFI extension required");
        }
        $this->tf = $this->getTensorflow();
    }

    private function getTensorflow(): TensorFlow {
        if(!isset($this->tensorflow)) {
            $this->tensorflow = new TensorFlow();
        }
        return $this->tensorflow;
    }

    /**
     * Preloads the model into memory
     * Do this for example after you've started your queue worker
     */
    public function boot(): void
    {
        $this->getSession();
    }

    /**
     * Runs the model to return the predicted output
     *
     * @param mixed $input
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
    public function close(): void
    {
        if(!isset(self::$_models[$this->getModelPath()])) {
            return;
        }
        $this->getSession()->close();
    }

    public function printGraph(): void
    {
        Helpers::printGraph($this->getSession()->getGraph());
    }

    protected function getSession(): Session
    {
        $modelPath = $this->getModelPath();
        if(!isset(self::$_models[$modelPath])) {
            self::$_models[$modelPath] = $this->tf->loadSavedModel($this->getModelPath());
        }
        return self::$_models[$modelPath];
    }

    /**
     * @param mixed $result
     *
     * @return mixed
     */
    protected function transformResult($result)
    {
        return $result;
    }

    public function getInputLayer(): string
    {
        return '';
    }
}
