<?php
require __DIR__.'/vendor/autoload.php';

/**
 * Configuration
 * We need some files (tensorflow itself, and the tensorflow models) to be able
 * to use FuncAI. Downloading these files happens automatically, but you need
 * to provide folders where we can store those files.
 *
 * These folders will have to be available on your production server
 * and we will store about 20 Mb of data in them.
 */

// Specify where the tensorflow models should be stored
\FuncAI\Config::setModelBasePath(realpath(dirname(__FILE__) . '/models/'));
// Specify where tensorflow should be downloaded to
\FuncAI\Config::setLibPath(dirname(__FILE__) . '/tensorflow/');

function similarity(&$a, &$b)
{
    $prod = 0.0;
    $v1_norm = 0.0;
    foreach ($a as $i=>$xi) {
        $prod += $xi*$b[$i];
        $v1_norm += $xi*$xi;
    }
    $v1_norm = sqrt($v1_norm);

    $v2_norm = 0.0;
    foreach ($b as $i=>$xi) {
        $v2_norm += $xi*$xi;
    }
    $v2_norm = sqrt($v2_norm);

    return $prod/($v1_norm*$v2_norm);
}

/**
 * Prediction
 * This is a sample prediction which uses a mobilenet model to generate a "feature vector" for the given image.
 * A feature vector is basically just an array of floating point numbers which define (in an abstract way) the contents of the image.
 * It's later possible to compare the feature vectors of different images to see if they are similar.
 */
$model = new \FuncAI\Models\MobileNetFeatureVector();
$output = $model->predict(__DIR__ . '/sample_data/butterfly.jpg');
$output2 = $model->predict(__DIR__ . '/sample_data/prince-akachi.jpg');

// The following line outputs the similarity
var_dump(similarity($output, $output2));
