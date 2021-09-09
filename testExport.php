<?php

use FuncAI\Applications\ImageClassification\ImageClassificationTrainingSample;

require __DIR__.'/vendor/autoload.php';

$ai = new \FuncAI\Applications\ImageClassification\ImageClassification();
$ai->addTrainingSample(new ImageClassificationTrainingSample('./sample_data/butterfly.jpg', 1));
$ai->addTrainingSample(new ImageClassificationTrainingSample('./sample_data/prince-akachi.jpg', 1));
$ai->addTrainingSample(new ImageClassificationTrainingSample('./sample_data/style.jpg', 2));
$ai->exportTrainingData('./sample_data');