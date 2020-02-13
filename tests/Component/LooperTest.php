<?php

namespace PHPMetro\Tests\Component\Looper;

use PHPUnit\Framework\TestCase;
use PHPMetro\Component\Looper;

class LooperTest extends TestCase
{
    public 
        $loop,
        $array;

    public function setUp(): void
    {
        $this->loop = new Looper();

        $this->loop->for(15, function()
        {
            $this->array[] = $this->loop->getIteration();
        });
    }

    public function testForLoop()
    {
        $this->assertEquals(
            15,
            \count($this->array)
        );

        $this->assertEquals(
            0,
            $this->array[0]
        );

        $this->assertEquals(
            14,
            $this->array[14]
        );
    }
}
