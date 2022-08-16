<?php

declare(strict_types=1);


interface FilterInterface
{
    /**
     * Get field name
     * @return string
     */
    public function getFieldName() : string;
    /**
     * Filter faceted data
     * @param array<int|string,array<int>|\SplFixedArray<int>> $facetedData
     * @param array<int,bool|int>|null $inputIdKeys - RecordId passed into keys of an array (performance issue)
     * @return array<int,bool> - results in keys
     */
    public function filterResults(array $facetedData, ?array $inputIdKeys = null) : array;
}