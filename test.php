<?php

require_once 'src/VCardParser.php';
require_once 'src/VCard.php';
require_once 'src/VCardField.php';

$parser = new VCardParser();

$cards = $parser->parseFile('sample.vcf');

foreach ($cards as $index => $card) {
    echo "Card " . ($index + 1) . PHP_EOL;

    foreach ($card->getFields() as $field) {
        echo $field->getName() . ': ' . $field->getValue() . PHP_EOL;
    }

    echo PHP_EOL;
}