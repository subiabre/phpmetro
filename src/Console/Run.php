<?php

namespace PHPMetro\Console;

use PHPMetro\Service\AnalysesTraverser;
use PHPMetro\Service\ConfigFinder;
use PHPMetro\Analysis\BaseAnalysis;
use SebastianBergmann\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Main PHPMetro runner
 */
class Run extends Command
{
    protected function configure()
    {
        $this->setName('run');
        $this->setDescription('Main PHPMetro runner.');
        $this->setHelp('This command will launch the loading of your config file and trigger the execution of your analysis suite');

        $this->addArgument('config', InputArgument::OPTIONAL);
        $this->addUsage('path/to/config.xml');
    }

    public function cleanClassName($class, $suffix, $namespace)
    {
        $breakName = explode("\\", trim($class, "\\"));
        $glueName = implode("\\\\", $breakName);

        $breakNamespace = explode("\\", $namespace);
        $glueNamespace = implode("\\\\\\\\", $breakNamespace);

        $removeNamespace = \preg_replace("/$glueNamespace\\\\\\\\/", '', $glueName);
        $removeSuffix = \preg_replace("/$suffix/", '', $removeNamespace);

        return $removeSuffix;
    }
    
    /**
     * @codeCoverageIgnore
     */
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $version = (new Version('X.Y.Z', \dirname(__DIR__, 2)))->getVersion();

        $output->writeln("PHPMetro $version by Facundo Daniel Subiabre");
        $output->writeln('');

        $configFinder = new ConfigFinder;
        $configLocation = $input->getArgument('config') ?: $configFinder->rootDir;

        $config = $configFinder->loadFrom($configLocation);

        if ($output->isVerbose() || $config->getVerbose())
        {
            $output->writeln("Configuration: " . $config->getLocation());
            $output->writeln("Runtime: PHP " . \phpversion());
            $output->writeln('');

            $startTime = \microtime(true);
        }

        foreach ($config->getSuites() as $suite) {
            
            if ($suite->getIgnore()) continue;

            $traverser = new AnalysesTraverser;

            $traverser->setSuffix($suite->getSuffix());
            $traverser->setNamespace($suite->getNamespace());
            $traverser->setDirectory($suite->getDirectory());

            $analyses = $traverser->getClasses();

            $output->writeln("> " . $suite->getName());

            foreach ($analyses as $file => $class) {
                include $file;

                /** @var BaseAnalysis */
                $do = new $class;
                $do->setUp();

                $cleanName = $this->cleanClassName($class, $suite->getSuffix(), $suite->getNamespace());

                $output->writeln(">> " . $cleanName . ":");

                if ($output->isVerbose() || $config->getVerbose()) {
                    $samples = \number_format(\count($do->sample), 0, '.', ',');
                    $records = \number_format($do->getSampleSize(), 0, '.', ',');

                    $output->writeln(">> " . $samples . " samples and " . $records . " records.");
                    
                    foreach ($do->sample as $sample => $values) {
                        $records = \number_format(\count($values));
                        $pools = \number_format(\count($do->getPools(\count($values))));

                        $output->writeln(">> " . $sample . ": " . $records . " records. " . $pools . " pools.");
                    }
                }

                $tests = $do->getAllTests();

                foreach ($tests as $test) {
                    $testName = \ltrim($test, 'test');

                    $output->writeln(">>> " . $testName . ": " . (string) $do->{$test}());
                }

                if ($output->isVerbose() || $config->getVerbose()) {
                    $endTime = \microtime(true);
                    $runTime = \substr($endTime - $startTime, 0, 6);
                    $memory = \substr(\memory_get_peak_usage() / (1024 * 1024), 0, 6);

                    if ($output->isVeryVerbose()) {
                        $runTime = $endTime - $startTime;
                        $memory = \memory_get_peak_usage() / (1024 * 1024);
                    }
    
                    $output->writeln('');
                    $output->writeln("<bg=green>Time: " . $runTime . "s, Memory: " . $memory . " MB</>");
                }

            }
        }

        return Command::SUCCESS;
    }
}
