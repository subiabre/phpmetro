<?php

namespace PHPMetro\Analysis;

/**
 * The Analysis Interface
 */
interface AnalysisInterface
{
    public function addSample(string $name, int $size, callable $function): void;

    public function getSample(string $name): ?array;

    public function getSampleSizeOf(string $name): ?int;

    public function getSampleSize(): int;

    public function getAllTests(): array;
}
