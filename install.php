<?php
require __DIR__.'/vendor/autoload.php';

// Specify where tensorflow should be downloaded to
\FuncAI\Config::setLibPath('./tensorflow/');

$tfi = new \FuncAI\Install\TensorflowInstaller();
$tfi->install();
