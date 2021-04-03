<?php

namespace FuncAI\Install;

use FuncAI\Config;
use PharData;

class TensorflowInstaller
{
    public function isInstalled()
    {
        if(!is_dir(Config::getLibPath())) {
            return false;
        }
        if(!$this->libIsInstalled()) {
            return false;
        }
        return true;
    }

    public function install()
    {
        $installPath = Config::getLibPath();
        echo "Starting to install Tensorflow to '$installPath'\n";
        if($this->isInstalled()) {
            echo "Tensorflow is already installed.\n";
            return;
        }
        if(!$this->libIsInstalled()) {
            echo "Installing libtensorflow...\n";
            $this->installLib();
        }

        echo "\nDone!\n\n";
    }

    private function libIsInstalled()
    {
        $requiredFiles = [
            'libtensorflow.so.2.3.0',
            'TensorflowLicense.txt',
            'libtensorflow_framework.so.2'
        ];
        foreach($requiredFiles as $requiredFile) {
            if(!file_exists(Config::getLibPath() . '/' . $requiredFile)) {
                return false;
            }
        }
        return true;
    }

    private function installLib()
    {
        if(!is_dir(Config::getLibPath())) {
            mkdir(Config::getLibPath(), 0777, true);
        }
        $this->downloadLib();
    }

    private function downloadLib()
    {
        echo "Downloading libtensorflow...\n";
        $tensorflowLib = 'https://storage.googleapis.com/tensorflow/libtensorflow/libtensorflow-cpu-linux-x86_64-2.3.0.tar.gz';
        $tmpfilePath = sys_get_temp_dir() . '/libtensorflow-cpu-linux-x86_64-2.3.0.tar.gz';
        $decompressedPath = sys_get_temp_dir() . '/libtensorflow-cpu-linux-x86_64-2.3.0.tar';
        $extractionPath = sys_get_temp_dir().'/libtensorflow';

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

        $files = [
            './lib/libtensorflow.so.2.3.0' => Config::getLibPath() . '/libtensorflow.so.2.3.0',
            './lib/libtensorflow_framework.so.2.3.0' => Config::getLibPath() . '/libtensorflow_framework.so.2',
            './LICENSE' => Config::getLibPath() . '/LICENSE',
            './THIRD_PARTY_TF_C_LICENSES' => Config::getLibPath() . '/THIRD_PARTY_TF_C_LICENSES',
        ];
        foreach($files as $from => $to) {
            $phar->extractTo($extractionPath, $from);
            rename(realpath($extractionPath. '/' . $from), $to);
        }

        unlink($tmpfilePath);
        unlink($decompressedPath);
        rmdir($extractionPath . '/lib');
        rmdir($extractionPath);
    }
}
