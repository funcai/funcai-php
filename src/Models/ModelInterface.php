<?php

namespace FuncAI\Models;

interface ModelInterface {
    function getModelPath();
    function getOutputTensor();
    function getInputData($input);
    function getInputLayer();
}
