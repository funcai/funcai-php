<?php
namespace FuncAI;

class Config {
    private static string $modelBasePath;
    private static string $libPath;

    /**
     * @return string
     */
    public static function getModelBasePath(): string
    {
        if(!isset(self::$modelBasePath)) {
            self::$modelBasePath = realpath(dirname(__FILE__) . '/../models/');
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
