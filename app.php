<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use App\Parsers\TransactionJsonParser;
use App\Services\BinInfo;
use App\Services\ExchangeRate;
use App\TransactionProcessor;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile('config/services.yaml');

$client = new GuzzleHttp\Client();

$binInfoService = new BinInfo($client, $config['services']['binlist']['base_uri']);
$exchangeRateService = new ExchangeRate($client, $config['services']['exchangeratesapi']['base_uri']);
$parser = new TransactionJsonParser();

if (isset($argv[1])) {
    $processor = new TransactionProcessor($binInfoService, $exchangeRateService, $parser);
    $transactions = $processor->processTransactions($argv[1]);
    echo implode("\n", $transactions->pluck('commission'));
}
