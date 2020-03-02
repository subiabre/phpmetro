<?php

namespace PHPMetro\Service;

use PHPMetro\Component\Config;

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

        echo PHP_EOL . "PHPMetro by Facundo Subiabre." . PHP_EOL;

        if ($config->getVerbose()) {
            echo PHP_EOL . "Configuration: " . $loader->getPath() . PHP_EOL;

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

            echo "  " . $suite->getName() . PHP_EOL;

            foreach ($analyses as $file => $class) {
                include $file;

                $do = new $class;
                $do->setUp();

                $className = \trim($class, $suite->namespace);

                echo "    " . $className . ":";
                
                if ($config->getVerbose()) {
                    $samples = \count($do->sample);
                    $records = 0;

                    foreach ($do->sample as $sample) {
                        $records += \count($sample);
                    }

                    echo $samples . " samples with " . $records . " records.";
                }

                if (!$do->isSettingUp) {
                    $tests = $do->getAllTests();

                    foreach ($tests as $test) {
                        $testName = \ltrim($test, 'test');

                        echo PHP_EOL . "      " . $testName . ": " . (string) $do->{$test}();
                    }
                }
            }

            if ($config->getVerbose()) {
                $endTime = \microtime(true);
                $runTime = $endTime - $startTime;
                $memory = \memory_get_peak_usage() / 1024;

                echo "Time: " . $runTime . ", Memory: " . $memory;
            }
        }
    }
}
