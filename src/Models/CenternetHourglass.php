<?php
namespace FuncAI\Models;

use FuncAI\Config;
use FuncAI\Tensorflow\Output;
use FuncAI\Tensorflow\TensorFlow;

class CenternetHourglass extends AbstractModel
{
    public function getModelPath()
    {
        return Config::getModelBasePath() . '/centernet-hourglass';
    }

    public function getOutputTensor()
    {
        $outputOperation = $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall');
        var_dump($outputOperation->output(0));
        var_dump($outputOperation->output(0)->type());
        exit;
        return [
            $outputOperation->output(0),
            $outputOperation->output(1),
            $outputOperation->output(2),
            $outputOperation->output(3),
            $outputOperation->output(4),
            $outputOperation->output(5),
        ];
    }

    public function getInputData($imagePath)
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
                $data[$idx] = (int)($r);
                $data[$idx + 1] = (int)($g);
                $data[$idx + 2] = (int)($b);
            }
        }
        return $ret;
    }

    public function getInputLayer()
    {
        return 'serving_default_input_tensor';
    }

    /**
     * @param Output $results
     * @return array|string[]
     */
    protected function transformResult($results)
    {
        return $results;
    }
}
