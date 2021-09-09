<?php

namespace FuncAI\Applications\ImageClassification;

use FuncAI\Applications\TrainingSample;

class ImageClassificationTrainingSample implements TrainingSample
{
    private string $imagePath;
    private int $class;

    public function __construct(string $imagePath = null, int $class = null)
    {
        $this->imagePath = $imagePath;
        $this->class = $class;
    }

    /**
     * @param int $class
     *
     * @return ImageClassificationTrainingSample
     */
    public function setClass(int $class): ImageClassificationTrainingSample
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param string $imagePath
     *
     * @return ImageClassificationTrainingSample
     */
    public function setImagePath(string $imagePath): ImageClassificationTrainingSample
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @return int
     */
    public function getClass(): int
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getImagePath(): string
    {
        return $this->imagePath;
    }
}
