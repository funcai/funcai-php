<?php

namespace Tests;

use FuncAI\Tensorflow\Tensor;
use FuncAI\Tensorflow\TensorFlow;

final class TensorflowGraphTest extends TensorFlowTestCase
{
    public function testItCanAddNumbers(): void
    {
        $tf = new TensorFlow();
        $sess = $tf->session();

        $ret = $sess->run(
            $tf->op(
                'Add',
                [
                    $tf->constant(1),
                    $tf->constant(2),
                ]
            )
        );

        $this->assertEquals(3, $ret->value());
    }

    public function testItCanHandleStrings(): void
    {
        $tf = new TensorFlow();
        $sess = $tf->session();
        $tensor = new Tensor();
        $tensor->init('Hello World');
        var_dump($tensor->value());
        $strings = ['Hello', ' World'];
        $join = $tf->op(
            'StringJoin',
            [[
                $tf->constant($strings[0]),
                $tf->constant($strings[1]),
            ]]
        );
        $ret = $sess->run($join);

        $this->assertEquals($strings[0] . $strings[1], $ret->value());
    }
}
