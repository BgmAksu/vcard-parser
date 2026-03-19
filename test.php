<?php

declare(strict_types=1);

require_once __DIR__ . '/src/VCard.php';
require_once __DIR__ . '/src/VCardField.php';
require_once __DIR__ . '/src/VCardParser.php';
require_once __DIR__ . '/src/JCardExporter.php';

use VCard\JCardExporter;
use VCard\VCardParser;

function printSeparator(string $title): void
{
    echo PHP_EOL;
    echo str_repeat('=', 80) . PHP_EOL;
    echo $title . PHP_EOL;
    echo str_repeat('=', 80) . PHP_EOL;
}

function printSubSeparator(string $title): void
{
    echo PHP_EOL;
    echo str_repeat('-', 80) . PHP_EOL;
    echo $title . PHP_EOL;
    echo str_repeat('-', 80) . PHP_EOL;
}

function readUserInput(string $prompt): string
{
    echo $prompt;
    $input = fgets(STDIN);

    if ($input === false) {
        return 'q';
    }

    return trim($input);
}

function printMenu(array $files): void
{
    printSeparator('VCARD PARSER INTERACTIVE TEST');

    echo 'Select a file to test:' . PHP_EOL . PHP_EOL;

    foreach ($files as $index => $filePath) {
        echo '  [' . ($index + 1) . '] ' . basename($filePath) . PHP_EOL;
    }

    echo PHP_EOL;
    echo 'Enter a number (1-' . count($files) . ') or "q" to exit.' . PHP_EOL;
}

function runTestFile(string $filePath): void
{
    printSeparator('Testing: ' . basename($filePath));

    try {
        $parser = new VCardParser();
        $report = $parser->parseFileWithReport($filePath);

        $cards = $report['cards'];
        $errors = $report['errors'];

        echo 'Valid vCards   : ' . count($cards) . PHP_EOL;
        echo 'Invalid vCards : ' . count($errors) . PHP_EOL;

        if ($cards !== []) {
            printSubSeparator('Parsed vCards');

            foreach ($cards as $index => $card) {
                echo 'Card #' . ($index + 1) . PHP_EOL;
                echo '  FN    : ' . ($card->getFullName() ?? '-') . PHP_EOL;
                echo '  EMAIL : ' . ($card->getEmail() ?? '-') . PHP_EOL;
                echo '  ORG   : ' . ($card->getOrganization() ?? '-') . PHP_EOL;
                echo '  TITLE : ' . ($card->getTitle() ?? '-') . PHP_EOL;
                echo PHP_EOL;
            }
        }

        if ($errors !== []) {
            printSubSeparator('Errors');

            foreach ($errors as $error) {
                echo '- ' . $error . PHP_EOL;
            }
        }

        if ($cards !== []) {
            $exporter = new JCardExporter();

            printSubSeparator('jCard (First Card)');
            echo $exporter->exportAsJson($cards[0]) . PHP_EOL;
        }
    } catch (Throwable $exception) {
        printSubSeparator('Fatal Error');
        echo $exception->getMessage() . PHP_EOL;
    }
}

$sampleDirectory = __DIR__ . '/vcard_samples';

$files = [
    $sampleDirectory . '/sample.vcf',
    $sampleDirectory . '/sample_fatal_nested.vcf',
    $sampleDirectory . '/sample_fatal_unclosed.vcf',
    $sampleDirectory . '/sample_no_blocks.vcf',
    $sampleDirectory . '/sample_empty.vcf',
];

$existingFiles = array_values(array_filter($files, 'is_file'));

if ($existingFiles === []) {
    printSeparator('ERROR');
    echo 'No sample files found.' . PHP_EOL;
    exit(1);
}

while (true) {
    printMenu($existingFiles);

    $input = readUserInput(PHP_EOL . 'Your choice: ');
    $inputLower = strtolower($input);

    if ($inputLower === 'q') {
        printSeparator('EXIT');
        echo 'Goodbye!' . PHP_EOL;
        break;
    }

    if (!ctype_digit($input)) {
        printSeparator('INVALID SELECTION');
        echo 'Please enter a valid number.' . PHP_EOL;
        continue;
    }

    $index = (int) $input - 1;

    if (!isset($existingFiles[$index])) {
        printSeparator('INVALID SELECTION');
        echo 'Number out of range.' . PHP_EOL;
        continue;
    }

    runTestFile($existingFiles[$index]);

    readUserInput(PHP_EOL . 'Press Enter to return to menu...');
}