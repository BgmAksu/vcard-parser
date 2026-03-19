<?php

declare(strict_types=1);

class VCard
{
    /**
     * @var VCardField[]
     */
    private array $fields = [];

    public function addField(VCardField $field): void
    {
        $this->fields[] = $field;
    }

    /**
     * @return VCardField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}