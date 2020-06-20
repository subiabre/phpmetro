<?php

namespace PHPMetro\Component;

/**
 * Easy handler for the phpmetro config file
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
class Config
{
    /**
     * @var SimpleXMLElement
     */
    public $xml;

    /**
     * Path to the config file
     * @var String
     */
    protected $location;

    /**
     * @param string $location Location of the .xml file
     */
    public function __construct(string $location)
    {
        $this->location = $location;
        $this->xml = \simplexml_load_file($this->location);
    }

    /**
     * Get the location of the config file
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Get the "verbose" attribute as a boolean
     * @return bool
     */
    public function getVerbose(): bool
    {
        if ($this->xml->attributes()->verbose !== NULL && $this->xml->attributes()->verbose == "true") {
            return true;
        }

        return false;
    }

    /**
     * Get the "bootstrap" attribute
     * @return string
     */
    public function getBootstrap(): string
    {
        return $this->xml->attributes()->bootstrap;
    }

    /**
     * Get the suites as an array
     * @return array
     */
    public function getSuites(): ?array
    {
        $suites = [];

        foreach ($this->xml->suites as $tags)
        {
            foreach ($tags as $suite) {
                $suites[] = new Suite($suite);
            }
        }

        return $suites;
    }
}
