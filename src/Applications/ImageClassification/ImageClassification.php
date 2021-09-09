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
                file_put_contents($classDirectory . '/' . $i . '.' . $extension, $readStream);
            }
        }
    }
}
