<?php

declare(strict_types=1);

namespace App;

use App\Collections\CollectionInterface;
use App\Enums\Countries;
use App\Models\Transaction;
use App\Parsers\ParserInterface;
use App\Services\BinInfo;
use App\Services\ExchangeRate;
use Exception;

class TransactionProcessor
{
    public function __construct(
        private BinInfo $binInfoService,
        private ExchangeRate $exchangeRateService,
        private ParserInterface $parser,
    ) {
    }

    public function processTransactions(string $inputFile): CollectionInterface
    {
        $collection = $this->parser->parse(file_get_contents($inputFile));

        foreach ($collection->all() as $transaction) {
            $commission = $this->calculateCommission($transaction);
            $transaction->setCommission((float) number_format(ceil($commission * 100) / 100, 2));
        }

        return $collection;
    }

    private function calculateCommission(Transaction $transaction): float
    {
        $bin = $transaction->getBin();
        $amount = $transaction->getAmount();
        $currency = $transaction->getCurrency();

        $binInfo = $this->binInfoService->get($bin);
        if (!isset($binInfo['country']['alpha2'])) {
            throw new Exception('Missing bin information for country');
        }
        $isEu = Countries::isEu($binInfo['country']['alpha2']);

        $rate = $this->exchangeRateService->get($currency);
        $amountFixed = ($currency === 'EUR' || $rate === 0) ? $amount : $amount / $rate;

        return $amountFixed * ($isEu ? 0.01 : 0.02);
    }
}
