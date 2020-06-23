<?php

namespace PHPMetro\Console;

use PHPMetro\Component\Config;
use PHPMetro\Service\AnalysesTraverser;
use PHPMetro\Service\ConfigFinder;
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
        $config = $input->getArgument('config') ? new Config($input->getArgument('config')) : (new ConfigFinder)->load();
        $version = (new Version('X.Y.Z', \dirname(__DIR__, 2)))->getVersion();

        $output->writeln("PHPMetro $version by Facundo Daniel Subiabre");

        if ($output->isVerbose() || $config->getVerbose())
        {
            $output->writeln("Configuration: " . $config->getLocation());
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

                $do = new $class;
                $do->setUp();

                $cleanName = $this->cleanClassName($class, $suite->getSuffix(), $suite->getNamespace());

                $output->writeln(">> " . $cleanName . ":");

                if ($output->isVerbose() || $config->getVerbose()) {
                    $samples = \count($do->sample);
                    $records = $do->getSampleSize();

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
