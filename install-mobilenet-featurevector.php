<?php
require __DIR__.'/../../autoload.php';

// Specify where tensorflow should be downloaded to
\FuncAI\Config::setLibPath('./tensorflow/');
\FuncAI\Config::setModelBasePath('./models');

$mobileNetFeatureVectorInstaller = new \FuncAI\Install\MobileNetFeatureVectorInstaller();
$mobileNetFeatureVectorInstaller->install();
