<?php

namespace link0\Finder\Interfaces;

use Illuminate\Support\Collection;
use link0\Finder\Interfaces\SearchResultsInterface;

interface FinderInterface {
    public function search(string $query, string $path, array $options): SearchResultsInterface;
    public function stop(string $searchId): bool;
    public function getSearchId(int|string $pid): string;
}