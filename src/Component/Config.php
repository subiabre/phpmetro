<?php

namespace PHPMetro\Component;

/**
 * PHPMetro config factory
 * @package PHPMetro
 * @author https://gitlab.com/subiabre
 * @license MIT
 */
class Config
{
    protected $filePath;

    /**
     * @param string $file Path to configuration file
     */
    public function __construct(?string $file = NULL)
    {
        if (!$file)
        {
            $this->filePath = $this->expectedFilePath();
        }
    }

    /**
     * Obtain the configuration file path
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Obtain the configuration object
     * @param string $file Path to configuration file \
     * PHPMetro will by default try to locate the file at the root of your project
     * @return object
     */
    public function getFromFile(?string $file = NULL): object
    {
        if (!$file)
        {
            $file = $this->expectedFilePath();
        }

        if (\file_exists($file)) {
            $this->filePath = $file;

            return \simplexml_load_file($file);
        }

        throw new \Exception("The required phpmetro config file is not present at the location '{$file}'.", 1);        
    }

    /**
     * PHPMetro is meant to be run from command line at the root folder
     * @return string
     */
    private function expectedFilePath(): string
    {
        return './phpmetro.xml';
    }
}
