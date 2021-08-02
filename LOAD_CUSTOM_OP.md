Place the .so file into the /tensorflow folder and then execute the following code to load the .so file:

    $status = new Status();
    TensorFlow::$ffi->TF_LoadLibrary(Config::getLibPath() . $lib, $status->c);
    var_dump($status->code());
    var_dump($status->error());

Run this code to test the zero_out op:

    $tf = new TensorFlow();
    $sess = $tf->session();
    $join = $tf->op("ZeroOut", [$tf->constant([[1,2],[3,4]], TensorFlow::INT32)]);
    $ret = $sess->run($join);
    var_dump($ret->value());

To generate a fitting .so file, make sure to generate it with tensorflow 2.3 (the same tf that we're using in funcai-php). 

For the [zero_out op](https://github.com/tensorflow/custom-op), make sure to change the ./configure file to run `pip3 install tensorflow-cpu==2.3`

Also, build it in the tensorflow/tensorflow:devel docker container ("Image": "sha256:8b9720d6272d478e769341adeaaca827b74e76e9e32c9278749c7f8777714ee6",)


Make sure to run bazel with this custom copt parameter `bazel build --copt="-D_GLIBCXX_USE_CXX11_ABI=0" oss_scripts/pip_package:build_pip_package` see https://github.com/tensorflow/tensorflow/issues/4989
