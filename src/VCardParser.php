<?php

declare(strict_types=1);

namespace VCard;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class VCardParser
{
    private const BEGIN_VCARD = 'BEGIN:VCARD';
    private const END_VCARD = 'END:VCARD';
    private const VERSION_FIELD = 'VERSION';
    private const SUPPORTED_VERSION = '4.0';

    /**
     * @return VCard[]
     */
    public function parseFile(string $path): array
    {
        $content = $this->readFileContents($path);

        return $this->parseString($content);
    }

    /**
     * @return array{cards: VCard[], errors: array<int, string>}
     */
    public function parseFileWithReport(string $path): array
    {
        $content = $this->readFileContents($path);

        return $this->parseStringWithReport($content);
    }

    /**
     * @return VCard[]
     */
    public function parseString(string $content): array
    {
        $result = $this->parseStringWithReport($content);

        if ($result['errors'] !== []) {
            $firstError = reset($result['errors']);

            if ($firstError !== false) {
                throw new InvalidArgumentException($firstError);
            }

            throw new InvalidArgumentException('An unknown parsing error occurred.');
        }

        return $result['cards'];
    }

    /**
     * @return array{cards: VCard[], errors: array<int, string>}
     */
    public function parseStringWithReport(string $content): array
    {
        $normalizedContent = str_replace(["\r\n", "\r"], "\n", $content);

        if (trim($normalizedContent) === '') {
            throw new InvalidArgumentException('Input content is empty.');
        }

        $lines = explode("\n", $normalizedContent);
        $lines = $this->unfoldLines($lines);

        $cards = $this->splitCards($lines);

        if ($cards === []) {
            throw new InvalidArgumentException('No valid VCARD blocks found.');
        }

        $validCards = [];
        $errors = [];

        foreach ($cards as $index => $cardLines) {
            try {
                $validCards[] = $this->parseCard($cardLines);
            } catch (Throwable $exception) {
                $cardNumber = $index + 1;
                $errors[] = "Card {$cardNumber}: " . $exception->getMessage();
            }
        }

        return [
            'cards' => $validCards,
            'errors' => $errors,
        ];
    }

    private function readFileContents(string $path): string
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException("File not found: {$path}");
        }

        if (!is_readable($path)) {
            throw new InvalidArgumentException("File is not readable: {$path}");
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException("Failed to read file: {$path}");
        }

        return $content;
    }

    /**
     * @param string[] $lines
     * @return string[]
     */
    private function unfoldLines(array $lines): array
    {
        $unfolded = [];

        foreach ($lines as $line) {
            if ($line === '') {
                $unfolded[] = $line;
                continue;
            }

            $firstCharacter = $line[0];

            if (($firstCharacter === ' ' || $firstCharacter === "\t") && !empty($unfolded)) {
                $unfolded[count($unfolded) - 1] .= substr($line, 1);
                continue;
            }

            $unfolded[] = $line;
        }

        return $unfolded;
    }

    /**
     * @param string[] $lines
     * @return array<int, array<int, string>>
     */
    private function splitCards(array $lines): array
    {
        $cards = [];
        $current = [];
        $insideCard = false;

        foreach ($lines as $line) {
            $normalizedLine = trim($line);

            if ($normalizedLine === self::BEGIN_VCARD) {
                if ($insideCard) {
                    throw new InvalidArgumentException('Nested BEGIN:VCARD detected.');
                }

                $insideCard = true;
                $current = [$normalizedLine];
                continue;
            }

            if ($insideCard) {
                $current[] = $line;
            }

            if ($normalizedLine === self::END_VCARD) {
                if (!$insideCard) {
                    throw new InvalidArgumentException('END:VCARD found without BEGIN:VCARD.');
                }

                $cards[] = $current;
                $current = [];
                $insideCard = false;
            }
        }

        if ($insideCard) {
            throw new InvalidArgumentException('Unclosed VCARD block detected.');
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
            $trimmedLine = trim($line);

            if (
                $trimmedLine === ''
                || $trimmedLine === self::BEGIN_VCARD
                || $trimmedLine === self::END_VCARD
            ) {
                continue;
            }

            $field = $this->parseLine($trimmedLine);

            if ($field !== null) {
                $card->addField($field);
            }
        }

        $this->validateCard($card);

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

        if ($name === null || trim($name) === '') {
            throw new InvalidArgumentException("Invalid property line: {$line}");
        }

        $parameters = [];

        foreach ($parts as $part) {
            if (!str_contains($part, '=')) {
                continue;
            }

            [$key, $parameterValue] = explode('=', $part, 2);

            $normalizedKey = strtoupper(trim($key));
            $normalizedParameterValue = trim($parameterValue);

            if ($normalizedKey === '') {
                throw new InvalidArgumentException("Invalid parameter in line: {$line}");
            }

            if ($normalizedParameterValue === '') {
                $parameters[$normalizedKey] = [];
                continue;
            }

            $values = array_map('trim', explode(',', $normalizedParameterValue));
            $values = array_values(array_filter(
                $values,
                static function (string $value): bool {
                    return $value !== '';
                }
            ));

            $parameters[$normalizedKey] = $values;
        }

        return new VCardField(trim($name), $value, $parameters);
    }

    private function validateCard(VCard $card): void
    {
        $versionFields = [];

        foreach ($card->getFields() as $field) {
            if ($field->getName() === self::VERSION_FIELD) {
                $versionFields[] = $field;
            }
        }

        if (count($versionFields) === 0) {
            throw new InvalidArgumentException('Each VCARD must contain exactly one VERSION field.');
        }

        if (count($versionFields) > 1) {
            throw new InvalidArgumentException('A VCARD must not contain multiple VERSION fields.');
        }

        $version = trim($versionFields[0]->getValue());

        if ($version !== self::SUPPORTED_VERSION) {
            throw new InvalidArgumentException(
                "Unsupported VCARD version: {$version}. Only vCard " . self::SUPPORTED_VERSION . ' is supported.'
            );
        }
    }
}