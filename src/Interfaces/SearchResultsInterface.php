<?php

namespace link0\Finder\Interfaces;

use Illuminate\Support\Collection;

interface SearchResultsInterface {
    public function getDuration(): float;

    public function setDuration(float $duration): void;

    public function getTotal(): int;

    public function setTotal(int $total): void;

    public function getPath(): string;

    public function setPath(string $path): void;

    public function getResults(): Collection;

    public function setResults(array|string|Collection $results): void;
}