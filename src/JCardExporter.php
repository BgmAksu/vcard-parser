<?php

declare(strict_types=1);

class JCardExporter
{
    /**
     * @return array<int, mixed>
     */
    public function export(VCard $card): array
    {
        $properties = [];

        foreach ($card->getFields() as $field) {
            $properties[] = [
                strtolower($field->getName()),
                $this->normalizeParameters($field->getParameters()),
                'text',
                $field->getValue(),
            ];
        }

        return ['vcard', $properties];
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