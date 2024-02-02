<?php

namespace link0\Finder\DTO;

use Illuminate\Support\Collection;
use link0\Finder\Enums\SearchContextType;
use link0\Finder\Interfaces\SearchContextInterface;

class SearchContextDTO implements SearchContextInterface {
	private SearchContextType $type;
	private string $line;
	private int $lineNumber;
	private Collection $additionalData;

	public function setLineNumber(int $lineNumber): void {
		$this->lineNumber = $lineNumber;
	}

	public function getLineNumber(): int {
		return $this->lineNumber;
	}

	public function setLine(string $line): void {
		$this->line = $line;
	}

	public function getLine(): string {
		return $this->line;
	}

	public function setType(SearchContextType $type): void {
		$this->type = $type;
	}

	public function getType(): SearchContextType {
		return $this->type;
	}

	public function toArray(): array {
		return get_object_vars($this);
	}

	public function getAdditionalData(): Collection {
		return $this->additionalData;
	}

    public function setAdditionalData(array|string|Collection $data): void {
		$this->additionalData = collect(is_string($data) ? array_filter(explode(PHP_EOL, $data)) : $data);
	}
}