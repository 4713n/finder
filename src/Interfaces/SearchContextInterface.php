<?php

namespace Link000\Finder\Interfaces;

use Illuminate\Support\Collection;
use Link000\Finder\Enums\SearchContextType;
use Illuminate\Contracts\Support\Arrayable;

interface SearchContextInterface extends Arrayable {
	public function setLineNumber(int $lineNumber): void;
	public function getLineNumber(): int;
	public function setLine(string $line): void;
	public function getLine(): string;
	public function setType(SearchContextType $type): void;
	public function getType(): SearchContextType;
	public function getAdditionalData(): Collection;
    public function setAdditionalData(array|string|Collection $data): void;
}