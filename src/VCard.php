<?php

declare(strict_types=1);

namespace VCard;

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

    /**
     * @return VCardField[]
     */
    public function getFieldsByName(string $name): array
    {
        $normalizedName = strtoupper($name);
        $matchedFields = [];

        foreach ($this->fields as $field) {
            if ($field->getName() === $normalizedName) {
                $matchedFields[] = $field;
            }
        }

        return $matchedFields;
    }

    public function getFirstFieldByName(string $name): ?VCardField
    {
        $fields = $this->getFieldsByName($name);

        if ($fields === []) {
            return null;
        }

        return $fields[0];
    }

    public function getFirstFieldValue(string $name): ?string
    {
        $field = $this->getFirstFieldByName($name);

        if ($field === null) {
            return null;
        }

        return $field->getValue();
    }
}