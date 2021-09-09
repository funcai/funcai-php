<?php
namespace FuncAI\Applications\ImageClassification;

use FuncAI\Applications\Application;
use InvalidArgumentException;

class ImageClassification extends Application {
    public function exportTrainingData(string $exportPath): void {
        $exportPath = realpath($exportPath) . '/image-classification-export';
        // TODO: Check if the folder already exists
        if(!is_dir($exportPath)) {
            if(!@mkdir($exportPath, 0777, true)) {
                throw new InvalidArgumentException('The export path ' . $exportPath . ' does not exist and can\'t be created. Please create it, or set the correct parent directory permissions.');
            }
        }
        if(!is_writable($exportPath)) {
            throw new InvalidArgumentException('The export path ' . $exportPath . ' is not writeable. Please make sure to set the correct directory permissions.');
        }
        $exportPath = $exportPath . '/image-classification-export';
        mkdir($exportPath, 0777, true);

        $classes = [];
        foreach ($this->trainingSamples as $trainingSample) {
            /** @var ImageClassificationTrainingSample $trainingSample */
            if(!isset($classes[$trainingSample->getClass()])) {
                $classes[$trainingSample->getClass()] = [];
            }
            $classes[$trainingSample->getClass()][] = $trainingSample;
        }

        foreach($classes as $class => $samples) {
            $classDirectory = $exportPath . '/' . $class;
            mkdir($classDirectory);
            foreach($samples as $i => $sample) {
                /** @var ImageClassificationTrainingSample $sample */
                $readStream = fopen($sample->getImagePath(), 'r');
                $filename = $sample->getImagePath();
                $expl = explode('.', $filename);
                $extension = end($expl);
                $outputPath = $classDirectory . '/' . $i . '.' . $extension;
                file_put_contents($outputPath, $readStream);
                $this->resizeImage($outputPath, 224, 224);
            }
        }
    }

    function resizeImage($sourceImage, $maxWidth, $maxHeight, $quality = 80)
    {
        // Obtain image from given source file.
        if (!$image = @imagecreatefromjpeg($sourceImage))
        {
            return false;
        }

        // Get dimensions of source image.
        list($origWidth, $origHeight) = getimagesize($sourceImage);

        if ($maxWidth == 0)
        {
            $maxWidth  = $origWidth;
        }

        if ($maxHeight == 0)
        {
            $maxHeight = $origHeight;
        }

        // Calculate ratio of desired maximum sizes and original sizes.
        $widthRatio = $maxWidth / $origWidth;
        $heightRatio = $maxHeight / $origHeight;

        // Ratio used for calculating new image dimensions.
        $ratio = min($widthRatio, $heightRatio);

        // Calculate new image dimensions.
        $newWidth  = (int)$origWidth  * $ratio;
        $newHeight = (int)$origHeight * $ratio;

        // Create final image with new dimensions.
        $newImage = imagecreatetruecolor($maxWidth, $maxHeight);
        $dstX = 0;
        $dstY = 0;
        if($newWidth > $newHeight) {
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
