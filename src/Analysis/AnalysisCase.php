<?php

namespace PHPMetro\Analysis;

/**
 * Base case to perform an Analysis
 * @package PHPMetro
 * @author subiabrewd@gmail.com
 */
abstract class AnalysisCase extends BaseAnalysis implements AnalysisInterface
{
    /**
     * Will be run once before all tests
     * Add your samples here
     */
    abstract public function setUp(): void;
}
