<?php
namespace FuncAI\Applications;

use FuncAI\Applications\TrainingSamples\TrainingSample;

abstract class Application {
    protected $trainingSamples = [];
    
    public function addTrainingSample(TrainingSample $sample) {
        $this->trainingSamples[] = $sample;
    }
}