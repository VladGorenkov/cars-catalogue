<?php


declare(strict_types=1);
/**
 * Range filter for faceted index. Filter item by range (min,max)
 * @package KSamuel\FacetedSearch\Filter
 */
class RangeFilter extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function filterResults(array $facetedData, ?array $inputIdKeys = null): array
    {
        /**
         * @var array{min:int|float|null,max:int|float|null} $value
         */
        $value = $this->getValue();

        $min = $value['min'] ?? null;
        $max = $value['max'] ?? null;

        if ($min === null && $max === null) {
            return [];
        }

        // collect list for different values of one property
        $limit = [];
        foreach ($facetedData as $value => $records) {
            if ($min !== null && (float)$value < (float)$min) {
                continue;
            }
            if ($max !== null && (float)$value > (float)$max) {
                continue;
            }
            if (empty($limit)) {
                /**
                 * @var array<int>|\SplFixedArray<int> $records
                 */
                if($records instanceof \SplFixedArray){
                    $limit = $records->toArray();
                }else{
                    $limit = $records;
                }
            } else {
                // array sum (faster than array_merge here)
                foreach ($records as $item){
                    $limit[] = $item;
                }
            }
        }

        if (empty($limit)) {
            return [];
        }

        $limitData = [];
        foreach ($limit as $v){
            $limitData[$v] = true;
        }

        if (empty($inputIdKeys)) {
            /**
             * @var array<int,bool>$limitData
             */
            return $limitData;
        }

        if (count($inputIdKeys) < count($limitData)) {
            $start = &$inputIdKeys;
            $compare = &$limitData;
        } else {
            $start = &$limitData;
            $compare = &$inputIdKeys;
        }

        $result = [];
        foreach ($start as $index => $exists) {
            /**
             * @var int $index
             */
            if (isset($compare[$index])) {
                $result[$index] = true;
            }
        }
        return $result;
    }

    /**
     * @param mixed|array{min:int|float,max:int|float} $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value): void
    {
        if (!is_array($value) || (!isset($value['min']) && !isset($value['max']))) {
            throw new \InvalidArgumentException('Wrong value format for RangeFilter. Expected format ["min"=>0,"max"=>100]');
        }
        $this->value = [
            'min' => $value['min'] ?? null,
            'max' => $value['max'] ?? null,
        ];
    }
}
