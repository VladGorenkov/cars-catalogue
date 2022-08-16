<?php

declare(strict_types=1);

require_once "\Indexer\IndexerInterface.php";

class RangeIndexer implements IndexerInterface
{
    /**
     * @var int
     */
    protected $step;

    public function __construct(int $step)
    {
        if($step <= 0){
            throw new \Exception('Invalid step value: '.$step);
        }
        $this->step = $step;
    }

    /**
     * @param array<int|string,array<int>> $indexContainer
     * @param int $recordId
     * @param array<int,int|float> $values
     * @return bool
     */
    public function add(&$indexContainer, int $recordId, array $values) : bool
    {
        foreach ($values as $value){
            $position = (int)((float) $value / $this->step);
            $position = ($position) * $this->step;
            $indexContainer[$position][] = $recordId;
        }
        return true;
    }
}