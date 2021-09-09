<?php
namespace FuncAI\Models;

use FuncAI\Config;
use FuncAI\Tensorflow\Output;
use FuncAI\Tensorflow\Tensor;
use FuncAI\Tensorflow\TensorFlow;
use FuncAI\Tensorflow\TensorflowException;

class Imagenet21k extends AbstractModel
{
    public function getModelPath(): string
    {
        return Config::getModelBasePath() . '/bit-m-r50x1';
    }

    public function getOutputTensor(): Output
    {
        $output = $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall')->output(0);

        // Get the top 5 results
        $topResults = $this->tf->op('TopKV2', [$output, $this->tf->constant(5, TensorFlow::INT32)], [], [], null, 1);

        return $topResults;
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
                $data[$idx] = (float)($r);
                $data[$idx + 1] = (float)($g);
                $data[$idx + 2] = (float)($b);
            }
        }
        return $ret;
    }

    public function getInputLayer(): string
    {
        return 'serving_default_input_1';
    }

    protected function transformResult($results)
    {
        return $this->getLabels($results[0]);
    }

    /**
     * @param  array<int, int> $results
     *
     * @return array<int, string>
     */
    private function getLabels(array $results): array
    {
        $labels = file($this->getModelPath() . '/labels.txt');
        return array_map(function($idx) use ($labels) {
            return trim($labels[$idx + 1]);
        }, $results);
    }
}
