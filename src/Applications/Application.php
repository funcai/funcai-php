<?php

namespace FuncAI\Applications;

abstract class Application
{
    /**
     * @var array<int, TrainingSample>
     */
    protected array $trainingSamples = [];

    public function addTrainingSample(TrainingSample $sample): self
    {
        $this->trainingSamples[] = $sample;

        return $this;
    }
}
