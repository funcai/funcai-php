<?php
require __DIR__.'/../../autoload.php';

// Specify where tensorflow should be downloaded to
\FuncAI\Config::setLibPath('./tensorflow/');
\FuncAI\Config::setModelBasePath('./models');

$stylizationInstaller = new \FuncAI\Install\StylizationInstaller();
$stylizationInstaller->install();
