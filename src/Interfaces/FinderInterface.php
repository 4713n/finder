<?php

namespace Link000\Finder\Interfaces;

use Illuminate\Support\Collection;
use Link000\Finder\Interfaces\SearchResultsInterface;

interface FinderInterface {
    public function search(string $query, string $path, array $options): SearchResultsInterface;
    public function stop(string $searchId): bool;
    public function getContext(string $query, string $filePath, array $options): Collection;
    public function getSearchId(int|string $pid): string;
}