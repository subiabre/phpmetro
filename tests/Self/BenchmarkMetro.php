<?php

namespace PHPMetro\Tests\Self;

use PHPMetro\Analysis\AnalysisCase;

class BenchmarkMetro extends AnalysisCase
{
    public function setUp(): void
    {
        $this->addSample('times', 1000000, function() {
            $start = \microtime(true);
            
            \random_int(0, 100);

            $end = \microtime(true);

            return [$start, $end];
        });
    }

    public function testTotalTime()
    {
        $times = $this->getSample('times');
        $total = 0;

        foreach ($times as $time)
        {
            $duration = $time[1] - $time[0];
            $total += $duration;
        }

        return $total;
    }
}
