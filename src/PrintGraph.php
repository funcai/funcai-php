<?php
namespace FuncAI;

function print_graph($g)
{
    foreach ($g->operations() as $op) {
        echo $op->name() . ": " . $op->type() . ", " . $op->device() . "\n";
        $count = $op->numInputs();
        for ($i = 0; $i < $count; $i++) {
            $in = $op->input($i);
            $out = $in->producer();
            echo "  in_$i: " . $in->typeName($i) .
                ", from " . $out->op()->name() . "/out_" . $out->index() . "\n";
        }
        $count = $op->numOutputs();
        for ($i = 0; $i < $count; $i++) {
            $out = $op->output($i);
            $num = $out->numConsumers();
            $s = "";
            if ($num) {
                $inputs = $out->consumers();
                $s = ", to (";
                $first = true;
                foreach ($inputs as $in) {
                    if (!$first) {
                        $s .= ", ";
                    } else {
                        $first = false;
                    }
                    $s .= $in->op()->name() . "/" . $in->index();
                }
                $s .= ")";
            }
            echo "  out_$i: " . $out->typeName($i) . "$s\n";
        }
        $i = 0;
        foreach ($op->controlInputs() as $ctl) {
            $i++;
            echo "  ctl_in_$i: " . $ctl->name() . "\n";
        }
        $i = 0;
        foreach ($op->controlOutputs() as $ctl) {
            $i++;
            echo "  ctl_out_$i: " . $ctl->name() . "\n";
        }
    }
    echo "\n";
}
