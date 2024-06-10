<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;
use TypeError;

class Transaction implements ModelInterface
{
    private ?float $commission = null;
    private function __construct(
        private string $bin,
        private float $amount,
        private string $currency
    ) {
    }

    public static function fromArray(array $data): ?self
    {
        try {
            if (!isset($data['bin'], $data['amount'], $data['currency'])) {
                throw new InvalidArgumentException('Missing required fields.');
            }

            return new self(
                (string) $data['bin'],
                (float) $data['amount'],
                (string) $data['currency']
            );
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage() . "\n";
            return null;
        } catch (TypeError $e) {
            echo "Invalid argument types.\n";
            return null;
        }
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCommission(): ?float
    {
        return $this->commission;
    }

    public function setCommission(?float $commission): void
    {
        $this->commission = $commission;
    }

    public function __get(string $name): mixed
    {
        $methodName = 'get' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
        throw new InvalidArgumentException("Property {$name} does not exist.");
    }
}
