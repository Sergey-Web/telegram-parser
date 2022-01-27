<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\Pure;

class UpdateTaskDto
{
    private ?string $name;

    private ?string $searchText;

    private ?string $searchType;

    public function __construct(Request $request)
    {
        $this->name = $request->name ?? null;
        $this->searchText = $request->search_text ?? null;
        $this->searchType = $request->search_type ?? null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }


    public function getSearchText(): ?string
    {
        $searchText = $this->searchText;
        if ($this->searchText !== null) {
            $searchText = $this->processSearchText($this->searchText);
        }

        return $searchText;
    }

    public function getSearchType(): ?string
    {
        return $this->searchType;
    }

    #[Pure]
    public function getData(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        $data['search_text'] = $this->getSearchText();

        if ($this->searchType !== null) {
            $data['search_type'] = $this->searchType;
        }

        return $data;
    }

    private function processSearchText($searchText): string
    {
        return implode(' | ', explode(',', mb_strtolower($searchText)));
    }
}
