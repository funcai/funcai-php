<?php
namespace FuncAI\Models;

use FuncAI\Config;
use FuncAI\Tensorflow\TensorFlow;

class EsrGAN extends AbstractModel
{
    /**
     * @var int
     */
    private $width;
    /**
     * @var int
     */
    private $height;

    public function getModelPath()
    {
        return Config::getModelBasePath() . '/esrgan';
    }

    public function getOutputTensor()
    {
        $output = $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall')->output(0);

        return $output;
    }

    public function getInputData($imagePath)
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
                $data[$idx] = (float)($r);
                $data[$idx + 1] = (float)($g);
                $data[$idx + 2] = (float)($b);
            }
        }
        return $ret;
    }

    public function getInputLayer()
    {
        return 'serving_default_input_0';
    }

    protected function transformResult($result)
    {
        return $this->saveImage($result);
    }

    private function saveImage($imageData)
    {
        $imageData = $imageData[0];
        $w = $this->width * 4;
        $h = $this->height * 4;
        $img = imagecreatetruecolor($w, $h);
        foreach($imageData as $y => $row) {
            foreach($row as $x => $color) {
                $color = array_map(function($c) {
                    return min(255,max(0,$c));
                }, $color);
                $c = imagecolorallocate($img, $color[0], $color[1], $color[2]);
                imagesetpixel($img, $x, $y, $c);
            }
        }
        $img = imagescale($img, $this->width*2, $this->height*2);
        imagejpeg($img, 'out.jpg');
    }
}
