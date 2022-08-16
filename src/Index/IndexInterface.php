<?php


declare(strict_types=1);



require_once __DIR__."\..\Filter\FilterInterface.php";
require_once __DIR__."\..\Indexer\IndexerInterface.php";

/**
 * Simple faceted index
 * @package KSamuel\FacetedSearch
 */
interface IndexInterface
{
    /**
     * Add record to index
     * @param int $recordId
     * @param array<int|string,array<int,mixed>> $recordValues -  ['fieldName'=>'fieldValue','fieldName2'=>['val1','val2']]
     * @return bool
     */
    public function addRecord(int $recordId, array $recordValues): bool;


    /**
     * Get field data section from index
     * @param string $fieldName
     * @return array<int|string,array<int>>
     */
    public function getFieldData(string $fieldName): array;


    /**
     * Get all records from index
     * @return array<int>
     */
    public function getAllRecordId(): array;

    /**
     * Get all records from index as map [$id1=>true,...]
     * @return array<int,bool>
     */
    public function getAllRecordIdMap(): array;

    /**
     * Add specialized indexer for field
     * @param string $fieldName
     * @param IndexerInterface $indexer
     */
    public function addIndexer(string $fieldName, IndexerInterface $indexer): void;

    /**
     * @param string $field
     * @param mixed $value
     * @return int
     */
    public function getRecordsCount(string $field, $value) : int;

    /**
     * Check if field exists
     * @param string $fieldName
     * @return bool
     */
    public function hasField(string $fieldName) : bool;

    /**
     * Get facet data.
     * @return array<int|string,array<int|string,array<int>|\SplFixedArray<int>>>
     */
    public function getData(): array;

    /**
     * Find acceptable filter values
     * @param array<FilterInterface> $filters
     * @param array<int> $inputRecords
     * @return array<string,array<int|string,int|string>>
     */
    public function aggregate(array $filters = [], array $inputRecords = [], bool $countValues = false): array;

    /**
     * Find records by filters as list of int
     * @param array<FilterInterface> $filters
     * @param array<int>|null $inputRecords - list of record id to search in. Use it for limit results
     * @return array<int>
     */
    public function find(array $filters, ?array $inputRecords = null): array;
}