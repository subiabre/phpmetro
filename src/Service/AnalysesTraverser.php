<?php

namespace PHPMetro\Service;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * Traverses trough given directory and maps the files inside
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
class AnalysesTraverser
{
    private
        $namespace,
        $directory,
        $suffix = '',
        $classes = []
    ;

    public
        $rootDir;

    public function __construct()
    {
        $this->rootDir = $this->locateRoot();
    }

    /**
     * Finds the root location
     * @return string Root location
     */
    private function locateRoot(): string
    {
        $root = false;
        $level = 1;

        while (!$root) {
            $dirname = \dirname(__DIR__, $level);

            if (\file_exists($dirname . '/vendor/autoload.php')) {
                $root = \rtrim($dirname, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            }
            
            $level++;
        }

        return $root;
    }

    /**
     * Update the directory location
     * @param string $directory Directory location
     * @return self
     */
    public function setDirectory(string $directory): self
    {
        $directory = \trim($directory, './\/\\');
        $this->directory = $this->rootDir . $directory;

        return $this;
    }

    /**
     * Obtain the path to the root folder of the project
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * Obtain an assocciative array of files in the directory and the class they should define
     * @return array
     */
    public function getClasses(): array
    {
        $suffix = $this->suffix;

        $directoryIterator = new RecursiveDirectoryIterator($this->directory);
        $recursive = new RecursiveIteratorIterator($directoryIterator);
        $iterator = new RegexIterator($recursive, "/[A-Za-z0-9]*{$suffix}.php/");
        
        $classes = [];

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $path = $file->getPathname();

                $class = \str_replace($this->directory . DIRECTORY_SEPARATOR, $this->namespace, $path);
                $class = \str_replace(DIRECTORY_SEPARATOR, '\\', $class);
                $class = \rtrim($class, '.php');

                $classes[$path] = $class;
            }
        }
    
        $this->classes = $classes;

        return $this->classes;
    }

    /**
     * Set the files suffix to use when traversing directory
     * @param string $suffix
     * @return self
     */
    public function setSuffix(string $suffix): self
    {
        $this->suffix = \rtrim($suffix, '.php');

        return $this;
    }

    /**
     * Set the namespace to attach to classes
     * @param string $namespace
     * @return self
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespace = '\\' . \trim($namespace, '\\') . '\\';

        return $this;
    }
}
