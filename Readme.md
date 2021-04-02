# FuncAI PHP
High performance, state of the art machine learning in php.

## Available applications

 - **Image recognition** - Get a label for a given image (in progress)
 - **Time series forecasting** - Predict what will happen in the future based on past events (planned)
 - **Sentiment analysis** - Find out if a user generated text is positive or negative (planned)
 - **Text summary** - Generate a summary from a given text (in progress)
 - **Image upscaling** - Increase the size of your images (planned)
 - **Translation** - Translate your text from one language to another (planned, https://huggingface.co/facebook/mbart-large-50-many-to-many-mmt)
 
If you have a usecase that's not listed above, please create an [issue](https://github.com/funcai/funcai-php/issues/new) and explain what you would like to do.

### Features

 - **Runs everywhere** - You only need PHP, **nothing else**
 - **Super simple API** - No machine learning knowledge required
 - **Maximum performance** - Preloads the machine learning core, so predictions are super fast

### Requirements
 - Either the provided dockerfile
 - Or PHP >= 7.4 on Linux

### Run with docker

 - Run `docker-compose up -d`
 - Run `docker-compose exec app bash`
 - Run `php example.php`

### About machine learning
todo
 - Pick the correct tasks (easy for computer, hard / repetitive for humans)
 - Training bias dilemma
 - Uncertainty
 - Specific tasks

### Architecture

 - Uses [FFI](https://www.php.net/manual/en/class.ffi.php) to talk to tensorflow for predictions
 - Uses a custom written c++ program for training (in progress)
 - Currently runs on linux with CPU support

### Todo
 - Add documentation on how to download efficientnet
 - Check for memory leaks

### Development

#### Git lfs
You need [git lfs](https://git-lfs.github.com/) to checkout this repository. After installing it run:

    git lfs pull

#### Docker (optional, but recommended)
Install [Docker](https://docs.docker.com/get-docker/) if you do not have it already.

#### Download the efficientnet model
To be able to run the example file you need to run the following docker command which will download the efficientnet model and save it in the correct file format:

    docker run -it --rm -v $PWD:/code -w /code tensorflow/tensorflow:2.3.0 python scripts/generate/efficientnet.py

Alternatively, if you already have python3 installed you can run:

    pip3 install tensorflow
    python3 scripts/generate/efficientnet.py

#### Run the provided Docker container (optional)
To start the provided Docker container which provides you with a working php7.4 installation run:

    docker-compose up -d

Afterwards run:

    docker-compose exec app bash

to get a Terminal inside of the docker container

#### Better phpstorm support (optional)
Go to your settings and open "Languages & Frameworks -> PHP -> PHP Runtime -> Others". Make sure "FFI" is checked. 

#### Manually installing libtensorflow (without git lfs)
Download libtensorflow from:
https://storage.googleapis.com/tensorflow/libtensorflow/libtensorflow-cpu-linux-x86_64-2.3.0.tar.gz
and place the contents of the lib folder of the tensorflow archive inside the lib folder in the root of the funcai-php project.
