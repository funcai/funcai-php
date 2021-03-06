<?php

namespace FuncAI\Install;

use FuncAI\Config;
use FuncAI\Models\Stylization;
use PharData;

class StylizationInstaller
{
    public function isInstalled(): bool
    {
        if (!is_dir(Config::getModelBasePath())) {
            return false;
        }
        if (!$this->modelIsInstalled()) {
            return false;
        }

        return true;
    }

    public function install(): void
    {
        if ($this->isInstalled()) {
            return;
        }
        $model = new Stylization();
        $installPath = $model->getModelPath();
        echo "Starting to install the Stylization model to '$installPath'\n";
        if ($this->isInstalled()) {
            echo "The stylization model is already installed.\n";

            return;
        }
        if (!$this->modelIsInstalled()) {
            echo "Installing model...\n";
            $this->installModel();
        }

        echo "\nDone!\n\n";
    }

    private function modelIsInstalled(): bool
    {
        $model = new Stylization();
        $requiredFiles = [
            'saved_model.pb',
        ];
        foreach ($requiredFiles as $requiredFile) {
            if (!file_exists($model->getModelPath() . '/' . $requiredFile)) {
                return false;
            }
        }

        return true;
    }

    private function installModel(): void
    {
        $model = new Stylization();
        if (!is_dir($model->getModelPath())) {
            mkdir($model->getModelPath(), 0777, true);
        }
        $this->downloadModel();
    }

    private function downloadModel(): void
    {
        echo "Downloading model...\n";
        $tensorflowLib = 'https://tfhub.dev/google/magenta/arbitrary-image-stylization-v1-256/2?tf-hub-format=compressed';
        $tmpfilePath = sys_get_temp_dir() . '/arbitrary-image-stylization-v1-256.tar.gz';
        $decompressedPath = sys_get_temp_dir() . '/arbitrary-image-stylization-v1-256.tar';
        $extractionPath = sys_get_temp_dir() . '/arbitrary-image-stylization';

        if (!file_exists($tmpfilePath)) {
            $tmpfile = fopen($tmpfilePath, 'w');
            $options = [
                CURLOPT_FILE => $tmpfile,
                CURLOPT_URL => $tensorflowLib,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_FAILONERROR => true,
            ];

            $handle = curl_init();
            curl_setopt_array($handle, $options);
            $result = curl_exec($handle);
            fclose($tmpfile);

            if ($result === false) {
                throw new \Exception(curl_error($handle));
            }
        }

        if (!file_exists($decompressedPath)) {
            $phar = new PharData($tmpfilePath);
            $phar->decompress();
        }
        $phar = new PharData($decompressedPath);

        $model = new Stylization();
        $modelPath = $model->getModelPath();

        $files = [
            './saved_model.pb' => $modelPath . '/saved_model.pb',
            './variables/variables.data-00000-of-00002' => $modelPath . '/variables/variables.data-00000-of-00002',
            './variables/variables.data-00001-of-00002' => $modelPath . '/variables/variables.data-00001-of-00002',
            './variables/variables.index' => $modelPath . '/variables/variables.index',
        ];
        foreach ($files as $from => $to) {
            if (!is_dir(dirname($to))) {
                mkdir(dirname($to), 0777, true);
            }
            $phar->extractTo($extractionPath, $from);
            rename(realpath($extractionPath . '/' . $from), $to);
        }

        //unlink($tmpfilePath);
        //unlink($decompressedPath);
        //$this->deleteDirectory($extractionPath);
    }

    private function deleteDirectory(string $dir): bool
    {
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
