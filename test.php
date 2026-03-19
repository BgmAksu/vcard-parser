<?php

declare(strict_types=1);

require_once 'src/VCardParser.php';
require_once 'src/VCard.php';
require_once 'src/VCardField.php';
require_once 'src/JCardExporter.php';

try {
    $parser = new VCardParser();
    $report = $parser->parseFileWithReport('sample.vcf');

    $cards = $report['cards'];
    $errors = $report['errors'];

    echo 'Valid vCards found: ' . count($cards) . PHP_EOL;
    echo 'Invalid vCards found: ' . count($errors) . PHP_EOL . PHP_EOL;

    foreach ($cards as $index => $card) {
        echo 'Valid Card ' . ($index + 1) . ':' . PHP_EOL;
        echo '  FN    : ' . ($card->getFirstFieldValue('FN') ?? '-') . PHP_EOL;
        echo '  EMAIL : ' . ($card->getFirstFieldValue('EMAIL') ?? '-') . PHP_EOL;
        echo '  ORG   : ' . ($card->getFirstFieldValue('ORG') ?? '-') . PHP_EOL;
        echo '  TITLE : ' . ($card->getFirstFieldValue('TITLE') ?? '-') . PHP_EOL;
        echo PHP_EOL;

        echo '  All Fields:' . PHP_EOL;

        foreach ($card->getFields() as $field) {
            echo '    ' . $field->getName() . ': ' . $field->getValue() . PHP_EOL;

            foreach ($field->getParameters() as $key => $values) {
                echo '      ' . $key . ' = [' . implode(', ', $values) . ']' . PHP_EOL;
            }
        }

        echo PHP_EOL;
    }

    if ($errors !== []) {
        echo 'Errors:' . PHP_EOL;

        foreach ($errors as $error) {
            echo '- ' . $error . PHP_EOL;
        }

        echo PHP_EOL;
    }

    if ($cards !== []) {
        $exporter = new JCardExporter();

        echo 'jCard export for first valid card:' . PHP_EOL;
        echo $exporter->exportAsJson($cards[0]) . PHP_EOL;
    }
} catch (Throwable $exception) {
    echo 'Fatal Error: ' . $exception->getMessage() . PHP_EOL;
}