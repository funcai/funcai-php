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
        $tensor->init('Hello');
        //var_dump($tensor->value());
        //exit;
        $join = $tf->op(
            'StringJoin',
            [[
                $tf->constant('Hello'),
                $tf->constant('World'),
            ]]
        );
        $ret = $sess->run($join);

        $this->assertEquals('Hello World', $ret->value());
    }
}
