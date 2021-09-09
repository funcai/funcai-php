<?php
namespace FuncAI\Models;

use FuncAI\Config;
use FuncAI\Tensorflow\Output;
use FuncAI\Tensorflow\Tensor;
use FuncAI\Tensorflow\TensorFlow;
use FuncAI\Tensorflow\TensorflowException;

class CenternetHourglass extends AbstractModel
{
    public function getModelPath(): string
    {
        return Config::getModelBasePath() . '/centernet-hourglass';
    }

    public function getOutputTensor(): Output
    {
        $outputOperation = $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall');
        // TODO: Find out which output is actually correct here
        return $outputOperation->output(0);
    }

    /**
     * @param string $imagePath
     *
     * @return Tensor
     * @throws TensorflowException
     */
    public function getInputData($imagePath): Tensor
    {
        $img = imagecreatefromjpeg($imagePath);
        // Todo: add black bars to not squish the image
        $img = imagescale($img, 224, 224);
        $w = imagesx($img);
        $h = imagesy($img);
        $ret = $this->tf->tensor(null, TensorFlow::INT8, [1, $w, $h, 3]);
        $data = $ret->data();

        // Convert the image data into a flat array
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $idx = ($y * $w * 3) + ($x * 3);
                $data[$idx] = $r;
                $data[$idx + 1] = $g;
                $data[$idx + 2] = $b;
            }
        }
        return $ret;
    }

    public function getInputLayer(): string
    {
        return 'serving_default_input_tensor';
    }

    /**
     * @param Output $results
     */
    protected function transformResult($results)
    {
        return $results;
    }
}
