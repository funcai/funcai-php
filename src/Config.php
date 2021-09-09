<?php
namespace FuncAI;

use Exception;

class Config {
    private static string $modelBasePath;
    private static string $libPath;

    /**
     * @return string
     */
    public static function getModelBasePath(): string
    {
        if(!isset(self::$modelBasePath)) {
            $basePath = realpath(dirname(__FILE__) . '/../models/');
            if(!$basePath) {
                throw new Exception('Could not get real path to the models directory.');
            }
            self::$modelBasePath = $basePath;
        }
        return self::$modelBasePath;
    }

    /**
     * @param string $modelBasePath
     */
    public static function setModelBasePath(string $modelBasePath): void
    {
        self::$modelBasePath = $modelBasePath;
    }

    /**
     * @return string
     */
    public static function getLibPath(): string
    {
        return self::$libPath;
    }

    /**
     * @param string $libPath
     */
    public static function setLibPath(string $libPath): void
    {
        self::$libPath = $libPath;
    }

}
