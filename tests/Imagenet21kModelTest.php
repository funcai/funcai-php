<?php

namespace Tests;

use FuncAI\Models\Imagenet21k;

final class Imagenet21kModelTest extends TensorFlowTestCase
{
    public function testItCanPredictCorrectly(): void
    {
        $model = new Imagenet21k();
        $output = $model->predict(__DIR__ . '/../sample_data/butterfly.jpg');

        $this->assertContains('silverspot', $output);
    }
}
