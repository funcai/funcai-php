<?php

namespace FuncAI\Install;

use FuncAI\Config;
use FuncAI\Models\Imagenet21k;
use FuncAI\Models\Stylization;
use PharData;

class Imagenet21kInstaller
{
    public function isInstalled()
    {
        if(!is_dir(Config::getModelBasePath())) {
            return false;
        }
        if(!$this->modelIsInstalled()) {
            return false;
        }
        return true;
    }

    public function install()
    {
        if($this->isInstalled()) {
            return;
        }
        $model = new Imagenet21k();
        $installPath = $model->getModelPath();
        echo "Starting to install the Imagenet21k model to '$installPath'\n";
        if($this->isInstalled()) {
            echo "The Imagenet21k model is already installed.\n";
            return;
        }
        if(!$this->modelIsInstalled()) {
            echo "Installing model...\n";
            $this->installModel();
        }

        echo "\nDone!\n\n";
    }

    private function modelIsInstalled()
    {
        $model = new Imagenet21k();
        $requiredFiles = [
            'saved_model.pb',
        ];
        foreach($requiredFiles as $requiredFile) {
            if(!file_exists($model->getModelPath() . '/' . $requiredFile)) {
                return false;
            }
        }
        return true;
    }

    private function installModel()
    {
        $model = new Imagenet21k();
        if(!is_dir($model->getModelPath())) {
            mkdir($model->getModelPath(), 0777, true);
        }
        $this->downloadModel();
        $this->downloadLabels();
    }

    private function downloadModel()
    {
        echo "Downloading model...\n";
        $tensorflowLib = 'https://tfhub.dev/google/bit/m-r50x1/imagenet21k_classification/1?tf-hub-format=compressed';
        $tmpfilePath = sys_get_temp_dir() . '/bit-m-r50x1.tar.gz';
        $decompressedPath = sys_get_temp_dir() . '/bit-m-r50x1.tar';
        $extractionPath = sys_get_temp_dir().'/bit-m-r50x1';

        if(!file_exists($tmpfilePath)) {
            $tmpfile = fopen($tmpfilePath, "w");
            $options = array(
                CURLOPT_FILE => $tmpfile,
                CURLOPT_URL => $tensorflowLib,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_FAILONERROR => true,
            );

            $handle = curl_init();
            curl_setopt_array($handle, $options);
            $result = curl_exec($handle);
            fclose($tmpfile);

            if ($result === false) {
                throw new \Exception(curl_error($handle));
            }
        }

        if(!file_exists($decompressedPath)) {
            $phar = new PharData($tmpfilePath);
            $phar->decompress();
        }
        $phar = new PharData($decompressedPath);

        $model = new Imagenet21k();
        $modelPath = $model->getModelPath();

        $files = [
            './saved_model.pb' => $modelPath . '/saved_model.pb',
            './variables/variables.data-00000-of-00001' => $modelPath . '/variables/variables.data-00000-of-00001',
            './variables/variables.index' => $modelPath . '/variables/variables.index',
        ];
        foreach($files as $from => $to) {
            if(!is_dir(dirname($to))) {
                mkdir(dirname($to), 0777, true);
            }
            $phar->extractTo($extractionPath, $from);
            rename(realpath($extractionPath. '/' . $from), $to);
        }

        unlink($tmpfilePath);
        unlink($decompressedPath);
        $this->deleteDirectory($extractionPath);
    }

    private function downloadLabels()
    {
        $model = new Imagenet21k();
        $modelPath = $model->getModelPath();
        $labels = file_get_contents('https://storage.googleapis.com/bit_models/imagenet21k_wordnet_lemmas.txt');
        file_put_contents($modelPath . '/labels.txt', $labels);
    }

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }
}
