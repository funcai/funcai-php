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

## Features

 - **Runs everywhere** - You only need PHP, **nothing else**
 - **Super simple API** - No machine learning knowledge required
 - **Maximum performance** - Preloads the machine learning core, so predictions are super fast

## Installation
#### 1. Install the package via composer:

    composer require funcai/funcai-php

#### 2. Download the tensorflow library:

    php vendor/funcai/funcai-php/install.php

This downloads tensorflow to `./tensorflow`.

#### 3. Download the required models
Until we've figured out a solution for how to host the final models, the following step is a bit more difficult than we'd like it to be.

You'll need to have python installed (locally), and some sort of way to host the model files yourself.

To generate the model, run:

    pip3 install tensorflow
    python3 vendor/funcai/funcai-php/scripts/generate/efficientnet.py

The model will be placed in vendor/funcai/funcai-php/models.

#### 4. Configure the models folder
You will need to move the models folder to a permanent location.
For example, move it to `/var/www/models` on your server. In that case make sure to set the base path accordingly:

    \FuncAI\Config::setModelBasePath('/var/www/models');

You can also just move the folder directly into your project and check them into git, but the folder might get quite big (100 Mb up to multiple Gb).

## Usage
After you've completed the installation steps you can run your first prediction:

    \FuncAI\Config::setLibPath('./tensorflow/'); // This should point to the path from step 2 
    $model = new \FuncAI\Models\EfficientNet();
    $output = $model->predict('./vendor/funcai/funcai-php/sample_data/butterfly.jpg');

## Requirements
 - PHP >= 7.4 on Linux
 - Or use the provided Dockerfile

### About machine learning
todo
- Pick the correct tasks (easy for computer, hard / repetitive for humans)
- Training bias dilemma
- Uncertainty
- Specific tasks

### How to run the docker file

 - Run `docker-compose up -d`
 - Run `docker-compose exec app bash`
 - Run `php example.php`

### Architecture

 - Uses [FFI](https://www.php.net/manual/en/class.ffi.php) to talk to tensorflow for predictions
 - Uses a custom written c++ program for training (in progress)
 - Currently runs on linux with CPU support

### Todo
 - Fix path of libtensorflow.so in tf_singlefile.h
 - Find a better way to download/host models
 - Check for memory leaks

### Development

#### Docker (optional, but recommended)
Install [Docker](https://docs.docker.com/get-docker/).

#### Download the efficientnet model
To be able to run the example file you need to run the following docker command which will download the efficientnet model and save it in the correct file format:

    docker run -it --rm -v $PWD:/code -w /code tensorflow/tensorflow:2.3.0 python scripts/generate/efficientnet.py

Alternatively, if you already have python3 installed you can just run:

    pip3 install tensorflow
    python3 scripts/generate/efficientnet.py

#### Run the provided Docker container (optional)
To start the provided Docker container which provides you with a working php7.4 installation run:

    docker-compose up -d

Afterwards run:

    docker-compose exec app bash

to get a Terminal inside of the docker container.

Alternatively you can just setup a PHP 7.4 environment locally and use that.

#### Better phpstorm support (optional)
Go to your settings and open "Languages & Frameworks -> PHP -> PHP Runtime -> Others". Make sure "FFI" is checked. 
