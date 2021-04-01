<?php
// The tensorflow classes are inspired by: https://github.com/dstogov/php-tensorflow

namespace FuncAI\Tensorflow;

use FFI;

class Tensor
{
    public $c;
    private $dataType;
    private $ndims;
    private $shape;
    private $nflattened;
    private $dataSize;
    private $status;

    public function init($value, $dataType = null, $shape = null, $status = null)
    {
        if (is_null($status)) {
            $status = new Status();
        }
        $this->status = $status;

        if (!is_null($value)) {
            if ($dataType == null) {
                $dataType = self::_guessType($value);
            }
            if ($shape == null) {
                $shape = self::_guessShape($value);
            }
        }

        $ndims = 0;
        $shapePtr = null;
        $nflattened = 1;
        if (is_array($shape)) {
            $ndims = count($shape);
            if ($ndims > 0) {
                $shapePtr = TensorFlow::$ffi->new("int64_t[$ndims]");
                $i = 0;
                foreach ($shape as $val) {
                    $shapePtr[$i] = $val;
                    $nflattened *= $val;
                    $i++;
                }
            }
        }
        if ($dataType == TensorFlow::STRING) {
            $nbytes = $nflattened * 8 + self::_byteSizeOfEncodedStrings($value);
        } else {
            $nbytes = TensorFlow::$ffi->TF_DataTypeSize($dataType) * $nflattened;
        }

        $this->c = TensorFlow::$ffi->TF_AllocateTensor($dataType, $shapePtr, $ndims, $nbytes);
        $this->dataType = $dataType;
        $this->shape = $shape;
        $this->ndims = $ndims;
        $this->nflattened = $nflattened;
        $this->dataSize = $nbytes;

        if (!is_null($value)) {
            $data = $this->data();
            if ($dataType == TensorFlow::STRING) {
                $this->_stringEncode($value, $data);
            } else {
                $this->_encode($value, $data);
            }
        }
    }

    private static function _guessType($value)
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                return self::_guessType($val);
            }
        }
        if (is_int($value)) {
            return PHP_INT_SIZE == 4 ? TensorFlow::INT32 : TensorFlow::INT64;
        } else if (is_double($value)) {
            return TensorFlow::DOUBLE;
        } else if (is_bool($value)) {
            return TensorFlow::BOOL;
        } else if (is_string($value)) {
            return TensorFlow::STRING;
        } else {
            throw new TensorflowException("Cannot guess type");
        }
    }

    private static function _guessShape($value, array $shape = [])
    {
        if (is_array($value)) {
            $shape[] = count($value);
            foreach ($value as $val) {
                return self::_guessShape($val, $shape);
            }
        }
        return $shape;
    }

    private static function _byteSizeOfEncodedStrings($value)
    {
        if (is_array($value)) {
            $size = 0;
            foreach ($value as $val) {
                $size += self::_byteSizeOfEncodedStrings($val);
            }
            return $size;
        } else {
            $val = (string)$value;
            return TensorFlow::$ffi->TF_StringEncodedSize(strlen($val));
        }
    }

    public function data()
    {
        static $map = [
            TensorFlow::FLOAT => "float",
            TensorFlow::DOUBLE => "double",
            TensorFlow::INT32 => "int32_t",
            TensorFlow::UINT8 => "uint8_t",
            TensorFlow::INT16 => "int16_t",
            TensorFlow::INT8 => "int8_t",
            TensorFlow::COMPLEX64 => null,
            TensorFlow::COMPLEX => null,
            TensorFlow::INT64 => "int64_t",
            TensorFlow::BOOL => "bool",
            TensorFlow::QINT8 => null,
            TensorFlow::QUINT8 => null,
            TensorFlow::QINT32 => null,
            TensorFlow::BFLOAT16 => null,
            TensorFlow::QINT16 => null,
            TensorFlow::QUINT16 => null,
            TensorFlow::UINT16 => "uint16_t",
            TensorFlow::COMPLEX128 => null,
            TensorFlow::HALF => null,
            TensorFlow::RESOURCE => null,
            TensorFlow::VARIANT => null,
            TensorFlow::UINT32 => "uint32_t",
            TensorFlow::UINT64 => "uint64_t",
        ];
        $n = $this->nflattened;
        if ($this->dataType == TensorFlow::STRING) {
            $m = $this->dataSize - $this->nflattened * 8;
            return TensorFlow::$ffi->cast(
                "struct {uint64_t offsets[$n]; char data[$m];}",
                $this->plainData());
        } else {
            $cast = @$map[$this->dataType];
            if (isset($cast)) {
                $cast .= "[$n]";
                return TensorFlow::$ffi->cast($cast, $this->plainData());
            } else {
                throw new TensorflowException("Not Implemented"); //???
            }
        }
    }

    public function plainData()
    {
        return TensorFlow::$ffi->TF_TensorData($this->c);
    }

    private function _stringEncode($value, $data, &$offset = 0, &$dim_offset = 0, $dim = 0, $n = 0)
    {
        if ($dim < $this->ndims) {
            $n = $this->shape[$dim];
            if (!is_array($value) || count($value) != $n) {
                throw new TensorflowException("value/shape mismatch");
            }
            $dim++;
            $i = 0;
            foreach ($value as $val) {
                $this->_stringEncode($val, $data, $offset, $dim_offset, $dim, $i);
                $i++;
            }
            return;
        }

        $str = (string)$value;
        $data->offsets[$dim_offset++] = $offset;
        $offset += TensorFlow::$ffi->TF_StringEncode(
            $str,
            strlen($str),
            $data->data + $offset,
            TensorFlow::$ffi->TF_StringEncodedSize(strlen($str)),
            $this->status->c);
        if ($this->status->code() != TensorFlow::OK) {
            throw new TensorflowException($this->status->error());
        }
    }

    private function _encode($value, $data, &$dim_offset = 0, $dim = 0, $n = 0)
    {
        if ($dim < $this->ndims) {
            $n = $this->shape[$dim];
            if (!is_array($value) || count($value) != $n) {
                throw new TensorflowException("value/shape mismatch");
            }
            $dim++;
            $i = 0;
            foreach ($value as $val) {
                $this->_encode($val, $data, $dim_offset, $dim, $i++);
            }
            return;
        }
        $data[$dim_offset++] = $value;
    }

    public function initFromC($cdata)
    {
        if (is_null($this->status)) {
            $this->status = new Status();
        }

        $this->c = $cdata;
        $this->dataType = TensorFlow::$ffi->TF_TensorType($cdata);
        $ndims = TensorFlow::$ffi->TF_NumDims($cdata);
        $this->ndims = $ndims;
        $this->nflattened = 1;
        for ($i = 0; $i < $ndims; $i++) {
            $dim = TensorFlow::$ffi->TF_Dim($cdata, $i);
            $this->shape[$i] = $dim;
            $this->nflattened *= $dim;
        }
        $this->dataSize = TensorFlow::$ffi->TF_TensorByteSize($cdata);
    }

    public function __destruct()
    {
        if (!is_null($this->c)) {
            TensorFlow::$ffi->TF_DeleteTensor($this->c);
        }
    }

    public function type()
    {
        return $this->dataType;
    }

    public function shape()
    {
        return $this->shape;
    }

    public function typeName()
    {
        return Helpers::getTypeName($this->dataType, $this->shape);
    }

    public function value()
    {
        $data = $this->data();
        if ($this->dataType == TensorFlow::STRING) {
            return $this->_stringDecode($data);
        } else {
            return $this->_decode($data);
        }
    }

    private function _stringDecode($data, &$dim_offset = 0, $dim = 0, $n = 0)
    {
        if ($dim < $this->ndims) {
            $n = $this->shape[$dim];
            $dim++;
            $ret = array();
            for ($i = 0; $i < $n; $i++) {
                $ret[$i] = $this->_stringDecode($data, $dim_offset, $dim, $i);
            }
            return $ret;
        }

        $offset = $data->offsets[$dim_offset++];

        $dst = TensorFlow::$ffi->new("char*[1]");
        $dst_len = TensorFlow::$ffi->new("size_t[1]");
        TensorFlow::$ffi->TF_StringDecode(
            $data->data + $offset,
            $this->dataSize - $offset,
            $dst,
            $dst_len,
            $this->status->c);
        if ($this->status->code() != TensorFlow::OK) {
            throw new TensorflowException($this->status->error());
        }
        return FFI::string($dst[0], $dst_len[0]);
    }

    private function _decode($data, &$dim_offset = 0, $dim = 0, $n = 0)
    {
        if ($dim < $this->ndims) {
            $n = $this->shape[$dim];
            $dim++;
            $ret = array();
            for ($i = 0; $i < $n; $i++) {
                $ret[$i] = $this->_decode($data, $dim_offset, $dim, $i);
            }
            return $ret;
        }
        return $data[$dim_offset++];
    }

    public function bytes()
    {
        if (!$this->isSerializable()) {
            throw new TensorflowException("Unserializable tensor");
        }
        return FFI::string($this->plainData(), $this->dataSize);
    }

    public function isSerializable()
    {
        static $serializable = [
            TensorFlow::FLOAT => 1,
            TensorFlow::DOUBLE => 1,
            TensorFlow::INT32 => 1,
            TensorFlow::UINT8 => 1,
            TensorFlow::INT16 => 1,
            TensorFlow::INT8 => 1,
            TensorFlow::COMPLEX64 => 1,
            TensorFlow::COMPLEX => 1,
            TensorFlow::INT64 => 1,
            TensorFlow::BOOL => 1,
            TensorFlow::QINT8 => 1,
            TensorFlow::QUINT8 => 1,
            TensorFlow::QINT32 => 1,
            TensorFlow::BFLOAT16 => 1,
            TensorFlow::QINT16 => 1,
            TensorFlow::QUINT16 => 1,
            TensorFlow::UINT16 => 1,
            TensorFlow::COMPLEX128 => 1,
            TensorFlow::HALF => 1,
            TensorFlow::UINT32 => 1,
            TensorFlow::UINT64 => 1,
        ];
        return isset($serializable[$this->dataType]);
    }

    public function setBytes(string $str)
    {
        if (!$this->isSerializable()) {
            throw new TensorflowException("Unserializable tensor");
        }
        if (strlen($str) != $this->dataSize) {
            throw new TensorflowException("Size mismatch");
        }
        FFI::memcpy($this->plainData(), $str, $this->dataSize);
    }
}
