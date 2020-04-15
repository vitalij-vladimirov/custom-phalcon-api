<?php
declare(strict_types=1);

namespace Common\Entity;

use Illuminate\Database\Eloquent\Collection;
use Common\BaseClasses\BaseEntity;

class PaginatedResult extends BaseEntity
{
    private int $totalResults;
    private int $totalPages;
    private int $currentPage;
    private int $resultsPerPage;
    private Collection $data;

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    public function setTotalResults(int $totalResults): PaginatedResult
    {
        $this->totalResults = $totalResults;
        return $this;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function setTotalPages(int $totalPages): PaginatedResult
    {
        $this->totalPages = $totalPages;
        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): PaginatedResult
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function getResultsPerPage(): int
    {
        return $this->resultsPerPage;
    }

    public function setResultsPerPage(int $resultsPerPage): PaginatedResult
    {
        $this->resultsPerPage = $resultsPerPage;
        return $this;
    }

    public function getData(): Collection
    {
        return $this->data;
    }

    public function setData(Collection $data): PaginatedResult
    {
        $this->data = $data;
        return $this;
    }
}
