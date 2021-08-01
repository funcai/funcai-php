<?php
namespace FuncAI\Models;

use FuncAI\Config;
use FuncAI\Tensorflow\Tensor;
use FuncAI\Tensorflow\TensorFlow;

class Hatespeech extends AbstractModel
{
    public function getModelPath()
    {
        return Config::getModelBasePath() . '/hatespeech_combined_bert_multi';
    }

    public function getOutputTensor()
    {
        $output = $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall')->output(0);

        // Get the top 5 results
        $topResults = $this->tf->op('TopKV2', [$output, $this->tf->constant(5, TensorFlow::INT32)], [], [], null, 1);

        return $topResults;
    }

    public function getInputData($text)
    {
        $stringTensor = new Tensor();
        $stringTensor->init([$text], TensorFlow::STRING);
        return $stringTensor;
    }

    public function getInputLayer()
    {
        return 'serving_default_input_1';
    }

    protected function transformResult($results)
    {
        return $this->getLabels($results);
    }

    private function getLabels($results)
    {
        print_r($results);
        return $results;
    }
}
