<?php

namespace PHPMetro\Tests;

use PHPMetro\Analysis\AnalysisCase;

class ImaginaryAnalysis extends AnalysisCase
{
    public function setUp(): void
    {
        $this->addSample('Null', 100, function() {
            return 2;
        });

        $this->addSample('Test', 100, function(): int {
            return 2;
        });
    }

    public function testTotalSum(): int
    {
        $sample = $this->getSample('Test');
        $total = 0;

        foreach ($sample as $value) {
            $total += $value;
        }

        return $total;
    }

    public function testNull()
    {
        // Tests that don't specify a return type are ignored
        return 'testNull';
    }
}
