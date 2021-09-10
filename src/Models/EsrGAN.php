<?php

namespace FuncAI\Models;

use FuncAI\Config;
use FuncAI\Tensorflow\Output;
use FuncAI\Tensorflow\Tensor;
use FuncAI\Tensorflow\TensorFlow;
use FuncAI\Tensorflow\TensorflowException;

class EsrGAN extends AbstractModel
{
    private int $width;
    private int $height;

    public function getModelPath(): string
    {
        return Config::getModelBasePath() . '/esrgan';
    }

    public function getOutputTensor(): Output
    {
        $output = $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall')->output(0);

        return $output;
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

        $this->width = imagesx($img);
        $this->height = imagesy($img);
        $ret = $this->tf->tensor(null, TensorFlow::FLOAT, [1, $this->width, $this->height, 3]);
        $data = $ret->data();

        // Convert the image data into a flat array
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $idx = ($y * $this->width * 3) + ($x * 3);
                $data[$idx] = (float) ($r);
                $data[$idx + 1] = (float) ($g);
                $data[$idx + 2] = (float) ($b);
            }
        }

        return $ret;
    }

    public function getInputLayer(): string
    {
        return 'serving_default_input_0';
    }

    protected function transformResult($result)
    {
        // TODO: return this differently, and/or let the user specify an output path
        $this->saveImage($result);
    }

    /**
     * @param array<int, array<int, array<int, array<int,int>>>> $imageData
     */
    private function saveImage(array $imageData): void
    {
        $imageData = $imageData[0];
        $w = $this->width * 4;
        $h = $this->height * 4;
        $img = imagecreatetruecolor($w, $h);
        foreach ($imageData as $y => $row) {
            foreach ($row as $x => $color) {
                $color = array_map(function ($c) {
                    return min(255, max(0, $c));
                }, $color);
                $c = imagecolorallocate($img, $color[0], $color[1], $color[2]);
                imagesetpixel($img, $x, $y, $c);
            }
        }
        $img = imagescale($img, $this->width * 2, $this->height * 2);
        imagejpeg($img, 'out.jpg');
    }
}
