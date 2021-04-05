<?php
#require __DIR__.'/../../autoload.php';
require __DIR__.'/vendor/autoload.php';

// Specify where tensorflow should be downloaded to
\FuncAI\Config::setLibPath('./tensorflow/');
\FuncAI\Config::setModelBasePath('./models');

$imagenet21kInstaller = new \FuncAI\Install\Imagenet21kInstaller();
$imagenet21kInstaller->install();
