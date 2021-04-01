# FuncAI PHP
High performance, state of the art machine learning in php.

## Available applications

 - **Image recognition** - Get a label for a given image (in progress)
 - **Time series forecasting** - Predict what will happen in the future based on past events (planned)
 - **Sentiment analysis** - Find out if a user generated text is positive or negative (planned)
 - **Text summary** - Generate a summary from a given text (planned)
 - **Image upscaling** - Increase the size of your images (planned)
 
If you have a usecase that's not listed above, please create an [issue](https://github.com/funcai/funcai-php/issues/new) and explain what you would like to do.

### Features

 - **Runs everywhere** - You only need PHP, **nothing else**
 - **Super simple API** - No machine learning knowledge required
 - **Maximum performance** - Preloads the machine learning core, so predictions are super fast

### Requirements

 - PHP >= 7.4
 - Linux

### About machine learning
todo
 - Pick the correct tasks (easy for computer, hard / repetitive for humans)
 - Training bias dilemma
 - Uncertainty
 - Specific tasks

### Architecture

 - Uses [FFI](https://www.php.net/manual/en/class.ffi.php) to talk to tensorflow for predictions
 - Uses a custom written c++ program for training
 - Currently runs on linux with CPU support

Download libtensorflow from:

https://storage.googleapis.com/tensorflow/libtensorflow/libtensorflow-cpu-linux-x86_64-2.3.0.tar.gz

and place the contents of the lib folder of the tensorflow archive inside the lib folder in the root of the funcai-php project.

Download the efficientnet model (todo)


### Development

#### Git lfs
You need [git lfs](https://git-lfs.github.com/) to checkout this repository.
Afterwards make sure you run `git lfs pull` to download the tensorflow libraries

#### Better phpstorm support
Go to your settings and open "Languages & Frameworks -> PHP -> PHP Runtime -> Others". Make sure "FFI" is checked. 
