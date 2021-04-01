<?php

namespace FuncAI\Models;

interface ModelInterface {
    function getModelPath();
    function getOutputTensor();
    function getInputTensor($input);
    function getInputLayer();
}
