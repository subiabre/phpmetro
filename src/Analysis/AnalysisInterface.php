<?php

namespace PHPMetro\Analysis;

use Closure;

/**
 * The Analysis Interface
 */
interface AnalysisInterface
{
    public function addSample(string $name, int $size, Closure $function): void;

    public function getSample(string $name): ?array;

    public function getSampleSizeOf(string $name): ?int;

    public function getSampleSize(): int;

    public function getAllTests(): array;
}
