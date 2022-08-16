<?php

declare(strict_types=1);

interface IndexerInterface
{
    /**
     * @param array<mixed> $indexContainer
     * @param int $recordId
     * @param array<mixed> $values
     * @return bool
     */
    public function add(array & $indexContainer, int $recordId, array $values) : bool;
}