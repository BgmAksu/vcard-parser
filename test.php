<?php

declare(strict_types=1);

require_once 'src/VCardParser.php';
require_once 'src/VCard.php';
require_once 'src/VCardField.php';

try {
    $parser = new VCardParser();
    $report = $parser->parseFileWithReport('sample.vcf');

    $cards = $report['cards'];
    $errors = $report['errors'];

    echo 'Valid vCards found: ' . count($cards) . PHP_EOL;
    echo 'Invalid vCards found: ' . count($errors) . PHP_EOL . PHP_EOL;

    foreach ($cards as $index => $card) {
        echo 'Valid Card ' . ($index + 1) . ':' . PHP_EOL;

        foreach ($card->getFields() as $field) {
            echo $field->getName() . ': ' . $field->getValue() . PHP_EOL;

            foreach ($field->getParameters() as $key => $values) {
                echo '  ' . $key . ' = [' . implode(', ', $values) . ']' . PHP_EOL;
            }
        }

        echo PHP_EOL;
    }

    if ($errors !== []) {
        echo 'Errors:' . PHP_EOL;

        foreach ($errors as $error) {
            echo '- ' . $error . PHP_EOL;
        }
    }
} catch (Throwable $exception) {
    echo 'Fatal Error: ' . $exception->getMessage() . PHP_EOL;
}