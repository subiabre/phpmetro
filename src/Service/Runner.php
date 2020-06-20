<?php

namespace PHPMetro\Service;

use SebastianBergmann\Version;

/**
 * Runner service, put's together the components to perform the analyses
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
class Runner
{
    public static function launch()
    {
        $loader = new ConfigFinder();
        $config = $loader->load();
        $version = new Version('X.Y.Z', __DIR__);

        echo "PHPMetro $version by Facundo Subiabre." . PHP_EOL;

        if ($config->getVerbose()) {
            echo "Configuration: " . $loader->getPath() . PHP_EOL;

            $startTime = \microtime(true);
        }

        foreach ($config->getSuites() as $suite) {
            if ($suite->getIgnore()) {
                continue;
            }

            $traverser = new AnalysesTraverser();

            $traverser->setSuffix($suite->getSuffix());
            $traverser->setNamespace($suite->getNamespace());
            $traverser->setDirectory($suite->getDirectory());

            $analyses = $traverser->getClasses();

            echo PHP_EOL . "  " . $suite->getName() . PHP_EOL;

            foreach ($analyses as $file => $class) {
                include $file;

                $do = new $class;
                $do->setUp();

                $className = \ltrim(\rtrim($class, $suite->getSuffix()), $suite->getNamespace());

                echo "    " . $className . ":";
                
                if ($config->getVerbose()) {
                    $samples = \count($do->sample);
                    $records = 0;

                    foreach ($do->sample as $sample) {
                        $records += \count($sample);
                    }

                    echo " " . $samples . " samples with " . $records . " records.";
                }

                if (!$do->isSettingUp) {
                    $tests = $do->getAllTests();

                    foreach ($tests as $test) {
                        $testName = \ltrim($test, 'test');

                        echo PHP_EOL . "      " . $testName . ": " . (string) $do->{$test}();
                    }

                    echo PHP_EOL;
                }
            }

            if ($config->getVerbose()) {
                $endTime = \microtime(true);
                $runTime = \substr($endTime - $startTime, 0, 5);
                $memory = \substr(\memory_get_peak_usage() / (1024 * 1024), 0, 5);

                echo PHP_EOL . "Time: " . $runTime . "s, Memory: " . $memory . " MB" . PHP_EOL;
            }
        }
    }
}
