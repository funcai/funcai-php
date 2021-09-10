<?php

use FuncAI\Applications\ImageClassification\ImageClassification;
use FuncAI\Applications\ImageClassification\ImageClassificationTrainingSample;

require __DIR__.'/vendor/autoload.php';


$ai = new ImageClassification();

$ai->addTrainingSample(new ImageClassificationTrainingSample('./sample_data/butterfly.jpg', 1));
$ai->addTrainingSample(new ImageClassificationTrainingSample('./sample_data/prince-akachi.jpg', 0));

$ai->exportTrainingData('./sample_data');
