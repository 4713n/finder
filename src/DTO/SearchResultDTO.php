<?php

namespace link0\Finder\DTO;

use Illuminate\Support\Collection;
use link0\Finder\Interfaces\SearchResultsInterface;

class SearchResultDTO implements SearchResultsInterface
{
    private float $duration;
    private int $total;
    private string $path;
    private Collection $results;

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): void
    {
        $this->duration = $duration;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getResults(): Collection
    {
        return $this->results;
    }

    public function setResults(array|string|Collection $results): void
    {
        $this->results = collect(is_string($results) ? explode(PHP_EOL, $results) : $results);
    }
}