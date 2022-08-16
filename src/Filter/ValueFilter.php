<?php


declare(strict_types=1);

require_once __DIR__."/AbstractFilter.php";

/**
 * Simple filter for faceted index. Filter item by value
 * @package KSamuel\FacetedSearch\Filter
 */
class ValueFilter extends AbstractFilter
{
    /**
     * @var array<int,mixed>
     */
    protected $value;

    /**
     * Set filter value
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void
    {
        if (!is_array($value)) {
            if (is_bool($value)) {
                $value = (int)$value;
            }
            if (is_float($value)) {
                $value = (string)$value;
            }
            $this->value = [$value];
            return;
        }

        foreach ($value as &$item) {
            if (is_bool($item)) {
                $item = (int)$item;
            }
            if (is_float($item)) {
                $item = (string)$item;
            }
        }
        unset($item);

        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function filterResults(array $facetedData, ?array $inputIdKeys = null): array
    {
        $result = [];
        $hasInput = !empty($inputIdKeys);

        // collect list for different values of one property
        foreach ($this->value as $item) {
            if (!isset($facetedData[$item])) {
                continue;
            }

            if (is_array($facetedData[$item])) {
                foreach ($facetedData[$item] as $recId) {
                    /**
                     * @var int $recId
                     */
                    if (!$hasInput) {
                        $result[$recId] = true;
                        continue;
                    }

                    if (isset($inputIdKeys[$recId])) {
                        $result[$recId] = true;
                    }
                }

            } else {
                // Performance patch SplFixedArray index access is faster than iteration
                $count = count($facetedData[$item]);
                for ($i = 0; $i < $count; $i++) {
                    $recId = $facetedData[$item][$i];
                    /**
                     * @var int $recId
                     */
                    if (!$hasInput) {
                        $result[$recId] = true;
                        continue;
                    }

                    if (isset($inputIdKeys[$recId])) {
                        $result[$recId] = true;
                    }
                }
            }
        }
        return $result;
    }
}