<?php

namespace FuncAI\Models\ImageClassification;

use FuncAI\Config;
use FuncAI\Models\AbstractModel;
use FuncAI\Tensorflow\Output;
use FuncAI\Tensorflow\Tensor;
use FuncAI\Tensorflow\TensorFlow;
use FuncAI\Tensorflow\TensorflowException;

class ImageClassificationBalanced extends AbstractModel
{
    private $modelTaskPath;

    /**
     * @param string $modelTaskPath
     * @throws TensorflowException
     */
    public function __construct(string $modelTaskPath)
    {
        parent::__construct();
        $this->modelTaskPath = $modelTaskPath;
    }

    public function getModelPath(): string
    {
        return Config::getModelBasePath() . '/' . $this->modelTaskPath . '/performance_balanced';
    }

    public function getOutputTensor(): Output
    {
        return $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall')->output(0);
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
                $data[$idx] = (float) ($r);
                $data[$idx + 1] = (float) ($g);
                $data[$idx + 2] = (float) ($b);
            }
        }

        return $ret;
    }

    public function getInputLayer(): string
    {
        return 'serving_default_keras_layer_input';
    }

    protected function transformResult($result)
    {
        return $result[0];
    }
}
