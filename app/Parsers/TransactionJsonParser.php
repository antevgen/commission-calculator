<?php

namespace App\Parsers;

use App\Collections\CollectionInterface;
use App\Collections\TransactionCollection;
use App\Models\Transaction;
use Exception;

class TransactionJsonParser implements ParserInterface
{
    public function parse(string $input): CollectionInterface
    {
        $lines = explode("\n", $input);
        $collection = new TransactionCollection();

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

            $transaction = Transaction::fromArray($data);
            if ($transaction !== null) {
                $collection->add($transaction);
            }
        }

        return $collection;
    }
}
