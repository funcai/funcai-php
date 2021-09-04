<?php
namespace Tests;

use FuncAI\Tensorflow\TensorFlow;

final class TensorflowGraphTest extends TensorFlowTestCase
{
    public function testItCanAddNumbers(): void
    {
        $tf = new TensorFlow();
        $sess = $tf->session();

        $ret = $sess->run(
            $tf->op("Add",
                [
                    $tf->constant(1),
                    $tf->constant(2),
                ]));

        $this->assertEquals(3, $ret->value());
    }

    public function testItCanHandleStrings(): void
    {
        $tf = new TensorFlow();
        $sess = $tf->session();

        $join = $tf->op("StringJoin",
            [[
                $tf->constant('Hello '),
                $tf->constant('Wörld')
            ]]);
        $ret = $sess->run($join);

        $this->assertEquals('Hello Wörld', $ret->value());
    }
}
