<?php

use FuncAI\Models\EfficientNet;

require __DIR__.'/vendor/autoload.php';

$model = new EfficientNet();
$output = $model->predict(__DIR__ . '/sample_data/butterfly.jpg');

echo 'The given image is of type: "' . $output . "\"\n";
// The given image is of type: "lycaenid, lycaenid butterfly"
