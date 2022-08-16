<?php


declare(strict_types=1);

namespace KSamuel\FacetedSearch\Indexer\Number;

require_once "\Indexer\IndexerInterface;

class RangeListIndexer implements IndexerInterface
{
    /**
     * @var array<int>
     */
    protected $ranges;

    /**
     * CustomRangeIndexer constructor.
     * @param array<int> $ranges
     */
    public function __construct(array $ranges)
    {
        sort($ranges);
        $this->ranges = $ranges;
    }

    /**
     * @param array<int|string,array<int>> $indexContainer
     * @param int $recordId
     * @param array<int,int|float> $values
     * @return bool
     */
    public function add(&$indexContainer, int $recordId, array $values): bool
    {
        foreach ($values as $value) {
            $indexContainer[$this->detectRangeKey($value)][] = $recordId;
        }
        return true;
    }

    /**
     * Detect range position key
     * @param int|float $value
     * @return int
     */
    protected function detectRangeKey($value): int
    {
        $lastKey = 0;
        foreach ($this->ranges as $key) {
            if ($value >= $lastKey && $value < $key) {
                return $lastKey;
            }
            $lastKey = $key;
        }
        return $lastKey;
    }
}