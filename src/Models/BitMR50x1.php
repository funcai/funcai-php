<?php
namespace FuncAI\Models;

use FuncAI\Config;
use FuncAI\Tensorflow\TensorFlow;

class BitMR50x1 extends AbstractModel
{
    public function getModelPath()
    {
        return Config::getModelBasePath() . '/bit-m-r50x1-feature';
    }

    public function getOutputTensor()
    {
        $output = $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall')->output(0);

        return $output;
    }

    public function getInputData($imagePath)
    {
        $img = imagecreatefromjpeg($imagePath);
        // Todo: add black bars to not squish the image
        $img = imagescale($img, 224, 224);
        $w = imagesx($img);
        $h = imagesy($img);
        $ret = $this->tf->tensor(null, TensorFlow::FLOAT, [1, $w, $h, 3]);
        $data = $ret->data();

        // Convert the image data into a flat array
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $idx = ($y * $w * 3) + ($x * 3);
                $data[$idx] = (float)($r);
                $data[$idx + 1] = (float)($g);
                $data[$idx + 2] = (float)($b);
            }
        }
        return $ret;
    }

    public function getInputLayer()
    {
        return 'serving_default_inputs';
    }

    protected function transformResult($results)
    {
        return $results[0];
    }

}
