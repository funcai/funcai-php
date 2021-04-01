Currently we are using tensorflow 2.3.0. If we want to update, we have to make sure to update the mapping code as well.

Here is some info on how to do that:

### To generate the .h file
- Go to https://github.com/tensorflow/tensorflow/blob/v2.3.0/tensorflow/c/c_api.h
- Copy the contents without the `#ifdef SWIG` stuff and without the `#ifdef __cplusplus` stuff.
- Replace the includes (`#include "tensorflow/c/tf_attrtype.h"` etc) with the contents of their files
- Remove all occurances of `TF_CAPI_EXPORT `


See https://github.com/serizba/cppflow/blob/b6c9ada247cbcf236371eb521e0e061ead213a6a/include/cppflow/ops.h for how to migrate the strings to tensorflow 2.4.0
