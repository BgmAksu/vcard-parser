<?php

declare(strict_types=1);

namespace VCard;

use RuntimeException;

class JCardExporter
{
    private const JCARD_LABEL = 'vcard';
    private const DEFAULT_VALUE_TYPE = 'text';

    /**
     * Export a single VCard object to a jCard-compatible array structure.
     *
     * @return array<int, mixed>
     */
    public function export(VCard $card): array
    {
        $properties = [];

        foreach ($card->getFields() as $field) {
            $properties[] = [
                strtolower($field->getName()),
                $this->normalizeParameters($field->getParameters()),
                self::DEFAULT_VALUE_TYPE,
                $field->getValue(),
            ];
        }

        return [self::JCARD_LABEL, $properties];
    }

    public function exportAsJson(VCard $card): string
    {
        $jCard = $this->export($card);

        $json = json_encode($jCard, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new RuntimeException('Failed to encode jCard as JSON.');
        }

        return $json;
    }

    /**
     * @param VCard[] $cards
     * @return array<int, array<int, mixed>>
     */
    public function exportMany(array $cards): array
    {
        $result = [];

        foreach ($cards as $card) {
            $result[] = $this->export($card);
        }

        return $result;
    }

    /**
     * @param VCard[] $cards
     */
    public function exportManyAsJson(array $cards): string
    {
        $jCards = $this->exportMany($cards);

        $json = json_encode($jCards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new RuntimeException('Failed to encode jCards as JSON.');
        }

        return $json;
    }

    /**
     * @param array<string, array<int, string>> $parameters
     * @return array<string, mixed>
     */
    private function normalizeParameters(array $parameters): array
    {
        $normalized = [];

        foreach ($parameters as $key => $values) {
            if (count($values) === 1) {
                $normalized[strtolower($key)] = $values[0];
                continue;
            }

            $normalized[strtolower($key)] = $values;
        }

        return $normalized;
    }
}