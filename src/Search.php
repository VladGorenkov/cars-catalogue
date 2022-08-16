<?php


declare(strict_types=1);

require_once "Filter\FilterInterface.php";
require_once "Index\IndexInterface.php";

/**
 * Class Search
 * Search in faceted index. Easily handles 100,000 products with 10 properties.
 * @package KSamuel\FacetedSearch
 */
class Search
{
    /**
     * @var IndexInterface
     */
    protected $index;

    /**
     * Search constructor.
     * @param IndexInterface $index
     */
    public function __construct(IndexInterface $index)
    {
        $this->index = $index;
    }

    /**
     * Find records by filters as list of int
     * @param array<FilterInterface> $filters
     * @param array<int>|null $inputRecords - list of record id to search in. Use it for limit results
     * @return array<int>
     */
    public function find(array $filters, ?array $inputRecords = null): array
    {
        return $this->index->find($filters, $inputRecords);
    }

    /**
     * Find acceptable filter values
     * @param array<FilterInterface> $filters
     * @param array<int> $inputRecords
     * @return array<string,array<int|string,int|string>>
     */
    public function findAcceptableFilters(array $filters = [], array $inputRecords = []): array
    {
        return $this->index->aggregate($filters, $inputRecords, false);
    }

    /**
     * Find acceptable filters with values count
     * @param array<FilterInterface> $filters
     * @param array<int> $inputRecords
     * @return array<string,array<int|string,int|string>>
     */
    public function findAcceptableFiltersCount(array $filters = [], array $inputRecords = []): array
    {
        return $this->index->aggregate($filters, $inputRecords, true);
    }
}