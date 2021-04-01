<?php
require_once("./TensorFlow2.php");
require_once("PrintGraph.php");

const MODEL = '../models/efficientnet';

function main()
{
    $tf = new TensorFlow();

    // Load the model
    $sess = $tf->loadSavedModel(MODEL);

    // Get the dummy output tensor
    $out = $tf->graph->operation('StatefulPartitionedCall')->output(0);

    // Get the best 3 results
    $out_label =
        $tf->op('Reshape', [
            $tf->op('TopKV2', [$out, $tf->constant(3, \TF\INT32)], [], [], null, 1),
            $tf->constant([-1])]);

    $inputTensor = getInputTensor($tf);
    $ret = $sess->run($out_label, ['serving_default_input_1' => $inputTensor]);
    $labels = $ret->value();
    print_r($labels);
}

function getInputTensor($tf)
{
    $image = 'snail.jpg';
    $img = imagecreatefromjpeg($image);
    $img = imagescale($img, 224, 224);
    $w = imagesx($img);
    $h = imagesy($img);
    $ret = $tf->tensor(null, \TF\FLOAT, [1, $w, $h, 3]);
    $data = $ret->data();
    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $idx = ($y * $w * 3) + ($x * 3);
            $data[$idx] = (float)($r);
            $data[$idx + 1] = (float)($g);
            $data[$idx + 2] = (float)($b);
        }
    }
    return $ret;
}

main();
