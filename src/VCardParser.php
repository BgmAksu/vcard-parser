<?php

declare(strict_types=1);

class VCardParser
{
    public function parseFile(string $path): array
    {
        $content = file_get_contents($path);

        return $this->parseString($content);
    }

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

    private function parseCard(array $lines): VCard
    {
        $card = new VCard();

        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            if (!str_contains($line, ':')) {
                continue;
            }

            [$name, $value] = explode(':', $line, 2);

            $field = new VCardField($name, $value);
            $card->addField($field);
        }

        return $card;
    }
}