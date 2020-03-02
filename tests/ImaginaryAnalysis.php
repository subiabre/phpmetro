<?php

namespace PHPMetro\Tests;

use PHPMetro\Analysis\AnalysisCase;

class ImaginaryAnalysis extends AnalysisCase
{
    public function setUp(): void
    {
        $this->addSample('Test', 100, function() {
            return 2;
        });
    }

    public function testTotalSum()
    {
        $sample = $this->getSample('Test');
        $total = 0;

        foreach ($sample as $value) {
            $total += $value;
        }

        return $total;
    }
}
