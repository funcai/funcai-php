<?php
// The tensorflow classes are inspired by: https://github.com/dstogov/php-tensorflow

namespace FuncAI\Tensorflow;

class Helpers {
    const TYPE_NAME = [
        TensorFlow::FLOAT => "FLOAT",
        TensorFlow::DOUBLE => "DOUBLE",
        TensorFlow::INT32 => "INT32",
        TensorFlow::UINT8 => "UINT8",
        TensorFlow::INT16 => "INT16",
        TensorFlow::INT8 => "INT8",
        TensorFlow::STRING => "STRING",
        TensorFlow::COMPLEX64 => "COMPLEX64",
        TensorFlow::COMPLEX => "COMPLEX",
        TensorFlow::INT64 => "INT64",
        TensorFlow::BOOL => "BOOL",
        TensorFlow::QINT8 => "QINT8",
        TensorFlow::QUINT8 => "QUINT8",
        TensorFlow::QINT32 => "QINT32",
        TensorFlow::BFLOAT16 => "BFLOAT16",
        TensorFlow::QINT16 => "QINT16",
        TensorFlow::QUINT16 => "QUINT16",
        TensorFlow::UINT16 => "UINT16",
        TensorFlow::COMPLEX128 => "COMPLEX128",
        TensorFlow::HALF => "HALF",
        TensorFlow::RESOURCE => "RESOURCE",
        TensorFlow::VARIANT => "VARIANT",
        TensorFlow::UINT32 => "UINT32",
        TensorFlow::UINT64 => "UINT64",
    ];

    public static function getTypeName($type, $shape)
    {
        if ($type < 100) {
            $name = self::TYPE_NAME[$type];
        } else {
            $name = '&' . self::TYPE_NAME[$type - 100];
        }
        if (is_array($shape) && count($shape) > 0) {
            $name .= '[' . implode(',', $shape) . ']';
        }
        return $name;
    }
}
