<?php

declare(strict_types=1);

class VCard
{
    private array $fields = [];

    public function addField(VCardField $field): void
    {
        $this->fields[] = $field;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}