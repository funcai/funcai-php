<?php

namespace FuncAI\Models;

use FuncAI\Config;
use FuncAI\Tensorflow\Output;
use FuncAI\Tensorflow\Tensor;
use FuncAI\Tensorflow\TensorFlow;

class Hatespeech extends AbstractModel
{
    public function getModelPath(): string
    {
        return Config::getModelBasePath() . '/hatespeech_combined_bert_multi';
    }

    public function getOutputTensor(): Output
    {
        $output = $this->tf->getDefaultGraph()->operation('StatefulPartitionedCall_2')->output(0);
        // Get the top 5 results
        // $topResults = $this->tf->op('TopKV2', [$output, $this->tf->constant(5, TensorFlow::INT32)], [], [], null, 1);

        return $output;
    }

    /**
     * @param string $text
     *
     * @return Tensor
     */
    public function getInputData($text): Tensor
    {
        $stringTensor = new Tensor();
        $stringTensor->init([$text], TensorFlow::STRING);

        return $stringTensor;
    }

    public function getInputLayer(): string
    {
        return 'serving_default_text';
    }

    protected function transformResult($results)
    {
        return $results;
    }
}
