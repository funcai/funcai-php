<?php
require __DIR__.'/../../autoload.php';

// Specify where tensorflow should be downloaded to
\FuncAI\Config::setLibPath('./tensorflow/');
\FuncAI\Config::setModelBasePath('./models');

$bitMR50x1Installer = new \FuncAI\Install\BitMR50x1Installer();
$bitMR50x1Installer->install();
