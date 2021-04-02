<?php
namespace FuncAI;

class Config {
    private static string $modelBasePath;

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

}
