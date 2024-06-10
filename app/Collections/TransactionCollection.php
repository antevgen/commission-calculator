<?php

namespace App\Collections;

use App\Models\ModelInterface;
use App\Models\Transaction;
use InvalidArgumentException;

class TransactionCollection implements CollectionInterface
{
    protected $transactions;

    public function __construct(array $transactions = [])
    {
        $this->transactions = $transactions;
    }

    public function add(ModelInterface $item): void
    {
        $this->transactions[] = $item;
    }

    public function pluck($property): array
    {
        return array_map(static function ($transaction) use ($property) {
            if (property_exists($transaction, $property)) {
                return $transaction->$property;
            }

            throw new InvalidArgumentException("Property {$property} does not exist on the transaction.");
        }, $this->transactions);
    }

    public function all(): array
    {
        return $this->transactions;
    }
}
