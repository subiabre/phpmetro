<?php

namespace PHPMetro\Analysis;

/**
 * The Analysis Interface
 */
interface AnalysisInterface
{
    public function setUp(): void;

    public function addSample(string $name, int $size, callable $function): void;

    public function getAllTests(): array;
}
