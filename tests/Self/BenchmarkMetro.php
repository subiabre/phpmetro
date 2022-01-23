<?php

namespace PHPMetro\Tests\Self;

use PHPMetro\Analysis\AnalysisCase;

class BenchmarkAnalysis extends AnalysisCase
{
    public function setUp(): void
    {
        $this->addSample('times', 1000000, function(): array {
            $start = \microtime(true);
            
            \random_int(0, 100);

            $end = \microtime(true);

            return ['start' => $start, 'end' => $end];
        });
    }

    public function testTotalTimeInSeconds(): float
    {
        $times = $this->getSample('times');
        $total = 0;

        foreach ($times as $time)
        {
            $duration = $time['end'] - $time['start'];
            $total += $duration;
        }

        return $total;
    }
}
