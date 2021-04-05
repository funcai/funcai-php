<?php
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
 * Installation
 * This downloads all necessary files and makes sure they are set up correctly.
 */
$tfi = new \FuncAI\Install\TensorflowInstaller();
$tfi->install();


/**
 * Prediction
 * This is a sample prediction which uses the efficientNet model to determine the contents
 * of an image.
 */
$model = new \FuncAI\Models\Stylization();
$output = $model->predict([
    __DIR__ . '/sample_data/prince-akachi.jpg',
    __DIR__ . '/sample_data/style.jpg',
]);

echo "Saved the stylized image to ./out.jpg";
