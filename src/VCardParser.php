<?php

declare(strict_types=1);

class VCardParser
{
    /**
     * @return VCard[]
     */
    public function parseFile(string $path): array
    {
        $content = file_get_contents($path);

        return $this->parseString($content);
    }

    /**
     * @return VCard[]
     */
    public function parseString(string $content): array
    {
        $lines = explode("\n", $content);

        $cards = [];
        $current = [];
        $insideCard = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VCARD') {
                $insideCard = true;
                $current = [];
                continue;
            }

            if ($line === 'END:VCARD') {
                $cards[] = $this->parseCard($current);
                $insideCard = false;
                continue;
            }

            if ($insideCard) {
                $current[] = $line;
            }
        }

        return $cards;
    }

    /**
     * @param string[] $lines
     */
    private function parseCard(array $lines): VCard
    {
        $card = new VCard();

        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            $field = $this->parseLine($line);

            if ($field !== null) {
                $card->addField($field);
            }
        }

        return $card;
    }

    private function parseLine(string $line): ?VCardField
    {
        if (!str_contains($line, ':')) {
            return null;
        }

        [$left, $value] = explode(':', $line, 2);

        $parts = explode(';', $left);
        $name = array_shift($parts);

        if ($name === null || $name === '') {
            return null;
        }

        $parameters = [];

        foreach ($parts as $part) {
            if (str_contains($part, '=')) {
                [$key, $parameterValue] = explode('=', $part, 2);
                $parameters[$key] = $parameterValue;
            }
        }

        return new VCardField($name, $value, $parameters);
    }
}