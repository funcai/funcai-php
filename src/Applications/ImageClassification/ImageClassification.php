<?php

namespace FuncAI\Applications\ImageClassification;

use Exception;
use FuncAI\Applications\Application;
use FuncAI\Models\ImageClassification\ImageClassificationBalanced;
use FuncAI\Models\ModelInterface;
use InvalidArgumentException;

class ImageClassification extends Application
{
    protected string $exportPath = 'image-classification-export';

    /**
     * @return ModelInterface
     * @throws Exception
     */
    protected function getModel(): ModelInterface
    {
        switch ($this->performance) {
            case self::PERFORMANCE_BALANCED:
                return new ImageClassificationBalanced($this->task);
            default:
                throw new Exception('Invalid performance setting');
        }
    }

    public function exportTrainingData(string $exportPath): string
    {
        $exportPath = realpath($exportPath) . '/' . $this->exportPath;

        if (!is_dir($exportPath)) {
            if (!@mkdir($exportPath, 0777, true)) {
                throw new InvalidArgumentException('The export path ' . $exportPath . ' does not exist and can\'t be created. Please create it, or set the correct parent directory permissions.');
            }
        } else {
            $this->deleteDirectory($exportPath);
            mkdir($exportPath);
        }

        if (!is_writable($exportPath)) {
            throw new InvalidArgumentException('The export path ' . $exportPath . ' is not writeable. Please make sure to set the correct directory permissions.');
        }

        $classes = [];
        foreach ($this->trainingSamples as $trainingSample) {
            /** @var ImageClassificationTrainingSample $trainingSample */
            if (!isset($classes[$trainingSample->getClass()])) {
                $classes[$trainingSample->getClass()] = [];
            }
            $classes[$trainingSample->getClass()][] = $trainingSample;
        }

        foreach ($classes as $class => $samples) {
            $classDirectory = $exportPath . '/' . $class;
            mkdir($classDirectory);
            foreach ($samples as $i => $sample) {
                /** @var ImageClassificationTrainingSample $sample */
                $readStream = fopen($sample->getImagePath(), 'r');
                $expl = explode('.', $sample->getImagePath());
                $extension = end($expl);
                $sampleOutputPath = $classDirectory . '/' . $i . '.' . $extension;
                file_put_contents($sampleOutputPath, $readStream);
                $this->resizeImage($sampleOutputPath, 224, 224);
            }
        }

        return $exportPath;
    }

    /**
     * @param string $dir
     *
     * @return bool
     * @throws Exception
     */
    private function deleteDirectory(string $dir): bool
    {
        // Make sure we only delete things that at least contain the export path
        if (strpos($dir, $this->exportPath) === false) {
            throw new Exception('Invalid directory deletion prevented: ' . $dir);
        }
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }
        $entries = scandir($dir);
        if ($entries) {
            foreach ($entries as $item) {
                if ($item == '.' || $item == '..') {
                    continue;
                }

                if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                    return false;
                }
            }
        }

        return rmdir($dir);
    }

    private function resizeImage(string $sourceImage, int $maxWidth, int $maxHeight, int $quality = 80): bool
    {
        // Obtain image from given source file.
        if (!$image = @imagecreatefromjpeg($sourceImage)) {
            return false;
        }

        // Get dimensions of source image.
        list($origWidth, $origHeight) = getimagesize($sourceImage);

        if ($maxWidth == 0) {
            $maxWidth = $origWidth;
        }

        if ($maxHeight == 0) {
            $maxHeight = $origHeight;
        }

        // Calculate ratio of desired maximum sizes and original sizes.
        $widthRatio = $maxWidth / $origWidth;
        $heightRatio = $maxHeight / $origHeight;

        // Ratio used for calculating new image dimensions.
        $ratio = min($widthRatio, $heightRatio);

        // Calculate new image dimensions.
        $newWidth = (int) $origWidth * $ratio;
        $newHeight = (int) $origHeight * $ratio;

        // Create final image with new dimensions.
        $newImage = @imagecreatetruecolor($maxWidth, $maxHeight);
        if (!$newImage) {
            throw new Exception('Could not create GD image stream. Please make sure GD is installed.');
        }
        $dstX = 0;
        $dstY = 0;
        if ($newWidth > $newHeight) {
            $dstY = ($maxHeight - $newHeight) / 2;
        } else {
            $dstX = ($maxWidth - $newWidth) / 2;
        }
        imagecopyresampled($newImage, $image, $dstX, $dstY, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagejpeg($newImage, $sourceImage, $quality);

        // Free up the memory.
        imagedestroy($image);
        imagedestroy($newImage);

        return true;
    }
}
