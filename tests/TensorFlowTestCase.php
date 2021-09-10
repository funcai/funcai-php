<?php

namespace Tests;

use FuncAI\Config;
use PHPUnit\Framework\TestCase;

abstract class TensorFlowTestCase extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // Specify where the tensorflow models should be stored
        Config::setModelBasePath(realpath(dirname(__FILE__) . '/../models/'));
        // Specify where tensorflow should be downloaded to
        Config::setLibPath(dirname(__FILE__) . '/../tensorflow/');
    }
}
