<?php

namespace Link000\Finder\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;

interface SearchResultsInterface extends Arrayable {
    public function getDuration(): float;

    public function setDuration(float $duration): void;

    public function getTotal(): int;

    public function setTotal(int $total): void;

    public function getPath(): string;

    public function setPath(string $path): void;

    public function getResults(): Collection;

    public function setResults(array|string|Collection $results): void;

	public function getAdditionalData(): Collection;

    public function setAdditionalData(array|string|Collection $data): void;
}