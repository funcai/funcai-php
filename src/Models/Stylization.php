<?php

namespace FuncAI\Models;

use FuncAI\Config;
use FuncAI\Tensorflow\Output;
use FuncAI\Tensorflow\Tensor;
use FuncAI\Tensorflow\TensorFlow;

class Stylization extends AbstractModel
{
    private int $width;
    private int $height;

    public function getModelPath(): string
    {
        return Config::getModelBasePath() . '/arbitrary-image-stylization';
    }

    public function getOutputTensor(): Output
    {
        $output = $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall')->output(0);

        return $output;
    }

    /**
     * @param mixed $input
     *
     * @return array<string, Tensor>
     */
    public function getInputData($input): array
    {
        return [
            'serving_default_placeholder' => $this->toImageTensor($input[0], true),
            'serving_default_placeholder_1' => $this->toImageTensor($input[1], true),
        ];
    }

    private function toImageTensor(string $path, bool $resize = false): Tensor
    {
        $img = imagecreatefromjpeg($path);
        if ($resize) {
            $img = imagescale($img, 256, 256);
        }
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
                $data[$idx] = (float) ($r / 255);
                $data[$idx + 1] = (float) ($g / 255);
                $data[$idx + 2] = (float) ($b / 255);
            }
        }

        return $ret;
    }

    public function getInputLayer(): string
    {
        return 'serving_default_input_0';
    }

    protected function transformResult($result): void
    {
        $this->saveImage($result);
    }

    /**
     * @param array<int, array<int, array<int, array<int,int>>>> $imageData
     */
    private function saveImage($imageData): void
    {
        $imageData = $imageData[0];
        $w = count($imageData);
        $h = count($imageData[0]);
        $img = imagecreatetruecolor($w, $h);
        foreach ($imageData as $y => $row) {
            foreach ($row as $x => $color) {
                $color = array_map(function ($c) {
                    $c *= 255;

                    return min(255, max(0, $c));
                }, $color);
                $c = imagecolorallocate($img, $color[0], $color[1], $color[2]);
                imagesetpixel($img, $x, $y, $c);
            }
        }
        imagejpeg($img, 'out.jpg');
    }
}
