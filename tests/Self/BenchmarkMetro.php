<?php

namespace PHPMetro\Tests\Self;

use PHPMetro\Analysis\AnalysisCase;

class BenchmarkMetro extends AnalysisCase
{
    public function setUp(): void
    {
        $this->addSample('times', 1000000, function(): array {
            $start = \microtime(true);

            usleep(1);

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

    public function testSampleSizeIsActual(): bool
    {
        $times = $this->getSample('times');

        return \count($times) == $this->getSampleSizeOf('times');
    }
}
