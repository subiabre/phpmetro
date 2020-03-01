<?php

namespace PHPMetro\Component;

use SimpleXMLElement;

/**
 * Object translation of an XML suite tag
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
class Suite
{
    private $suite;

    public function __construct(SimpleXMLElement $suite)
    {
        $this->suite = $suite;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return (string) $this->suite->attributes()->name;
    }

    /**
     * @return bool
     */
    public function getIgnore(): bool
    {
        if ($this->suite->attributes()->ignore !== NULL && $this->suite->attributes()->ignore == "true") {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        if ($this->suite->attributes()->suffix !== NULL) {
            return $this->suite->attributes()->suffix;
        }

        return 'Analysis';
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->suite->namespace;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->suite->directory;
    }
}
