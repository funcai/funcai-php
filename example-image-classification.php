<?php

use FuncAI\Applications\ImageClassification\ImageClassification;
use FuncAI\Tensorflow\TensorFlow;

require __DIR__.'/vendor/autoload.php';

/**
 * Configuration
 * We need some files (tensorflow itself, and the tensorflow models) to be able
 * to use FuncAI. Downloading these files happens automatically, but you need
 * to provide folders where we can store those files.
 *
 * These folders will have to be available on your production server
 * and we will store about 300 Mb of data in them.
 */

// Specify where the tensorflow models should be stored
\FuncAI\Config::setModelBasePath(realpath(dirname(__FILE__) . '/models/'));
// Specify where tensorflow should be downloaded to
\FuncAI\Config::setLibPath(dirname(__FILE__) . '/tensorflow/');


/**
 * Prediction
 * This is a sample prediction which uses the efficientNet model to determine the contents
 * of an image.
 */
$ai = new ImageClassification();
$ai->setTask('bread-classification');
$ai->setPerformance(ImageClassification::PERFORMANCE_BALANCED);
$output = $ai->predict(__DIR__ . '/sample_data/butterfly.jpg');
var_dump($output);
