<?php
require __DIR__.'/../../autoload.php';

// Specify where tensorflow should be downloaded to
\FuncAI\Config::setLibPath('./tensorflow/');

$tfi = new \FuncAI\Install\TensorflowInstaller();
$tfi->install();
