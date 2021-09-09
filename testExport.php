<?php

use FuncAI\Applications\ImageClassification\ImageClassification;
use FuncAI\Applications\ImageClassification\ImageClassificationTrainingSample;

require __DIR__.'/vendor/autoload.php';

$data = json_decode(file_get_contents('./recipe_images_202109091810.json'), true);

$ai = new ImageClassification();
var_dump(count($data['recipe_images']));
foreach($data['recipe_images'] as $i => $image) {
    if($image['deleted_at'] !== null) {
        continue;
    }
    $ai->addTrainingSample(new ImageClassificationTrainingSample('https://brotheld.de/' . $image['path'], 1));
}

$ai->exportTrainingData('./sample_data');
