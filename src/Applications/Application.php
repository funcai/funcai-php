<?php

namespace FuncAI\Applications;

use Exception;
use FuncAI\Models\ModelInterface;

abstract class Application
{
    const PERFORMANCE_BALANCED = 'performance_balanced';

    /**
     * @var array<int, TrainingSample>
     */
    protected array $trainingSamples = [];
    protected string $task;
    protected string $performance = self::PERFORMANCE_BALANCED;

    /**
     * Actually runs the model with the given input
     * and returns the output that the model predicted.
     *
     * @param mixed $input
     * @return mixed
     * @throws Exception
     */
    public function predict($input)
    {
        $model = $this->getModel();

        return $model->predict($input);
    }

    /**
     * Add one training sample to the list of training samples
     * After you've added all of your training samples you can call
     * the `exportTrainingData` method.
     *
     * @param TrainingSample $sample
     * @return $this
     */
    public function addTrainingSample(TrainingSample $sample): self
    {
        $this->trainingSamples[] = $sample;

        return $this;
    }

    public function resetTrainingSamples(): void
    {
        $this->trainingSamples = [];
    }

    /**
     * @throws Exception
     * @return ModelInterface
     */
    protected function getModel()
    {
        throw new Exception('Please implement the getModel method');
    }

    /**
     * @param string $task
     * @return Application
     */
    public function setTask(string $task): Application
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return string
     */
    public function getTask(): string
    {
        return $this->task;
    }

    /**
     * @param string $performance
     * @return Application
     */
    public function setPerformance(string $performance): Application
    {
        $this->performance = $performance;

        return $this;
    }

    /**
     * @return string
     */
    public function getPerformance(): string
    {
        return $this->performance;
    }
}
