<?php

namespace link0\Finder\Interfaces;

use Illuminate\Support\Collection;

interface FinderInterface {
    public function search(string $query, string $path, array $options): Collection;
}