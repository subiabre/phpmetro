<?php

namespace PHPMetro\Console;

use PHPMetro\Component\Config;
use PHPMetro\Service\AnalysesTraverser;
use PHPMetro\Service\ConfigFinder;
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

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $config = $input->getArgument('config') ? new Config($input->getArgument('config')) : (new ConfigFinder)->load();

        if ($output->isVerbose() || $config->getVerbose())
        {
            $output->writeln("Configuration: " . $config->getLocation());

            $startTime = \microtime(true);
        }

        foreach ($config->getSuites() as $suite) {
            
            if ($suite->getIgnore()) continue;

            $traverser = new AnalysesTraverser;

            $traverser->setSuffix($suite->getSuffix());
            $traverser->setNamespace($suite->getNamespace());
            $traverser->setDirectory($suite->setDirectory());

            $analyses = $traverser->getClasses();

            $output->writeln("> " . $suite->getName());

            foreach ($analyses as $file => $class) {
                include $file;

                $do = new $class;
                $do->setUp();

                $className = \ltrim(\rtrim($class, $suite->getSuffix()), $suite->getNamespace());

                $output->writeln(">> " . $className . ":");

                if ($output->isVerbose() || $config->getVerbose()) {
                    $samples = \count($do->sample);
                    $records = 0;

                    foreach ($do->sample as $sample) {
                        $records += \count($sample);
                    }

                    $output->writeln(">> " . $samples . " samples with " . $records . " records.");
                }

                if (!$do->isSettingUp) {
                    $tests = $do->getAllTests();

                    foreach ($tests as $test) {
                        $testName = \ltrim($test, 'test');

                        $output->writeln(">>> " . $testName . ": " . (string) $do->{$test}());
                    }
                }

                if ($output->isVerbose() || $config->getVerbose()) {
                    $endTime = \microtime(true);
                    $runTime = \substr($endTime - $startTime, 0, 5);
                    $memory = \substr(\memory_get_peak_usage() / (1024 * 1024), 0, 5);
    
                    $output->writeln("Time: " . $runTime . "s, Memory: " . $memory . " MB");
                }

            }
        }

        return Command::SUCCESS;
    }
}
