<?php


declare(strict_types=1);



abstract class AbstractFilter implements FilterInterface
{
    /**
     * @var string
     */
    protected $fieldName;
    /**
     * @var mixed
     */
    protected $value;

    /**
     * AbstractFilter constructor.
     * @param string $fieldName
     * @param mixed $value
     */
    public function __construct(string $fieldName, $value = null)
    {
        $this->fieldName = $fieldName;
        if ($value !== null) {
            $this->setValue($value);
        }
    }

    /**
     * @inheritDoc
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * Set filter value
     * @param mixed $value
     * @return void
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * Get filter value
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    abstract public function filterResults(array $facetedData, ?array $inputIdKeys = null): array;
}