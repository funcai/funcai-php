<?php
namespace FuncAI\Applications;

abstract class Application {
    protected $trainingSamples = [];

    public function addTrainingSample(TrainingSample $sample) {
        $this->trainingSamples[] = $sample;
    }
}
