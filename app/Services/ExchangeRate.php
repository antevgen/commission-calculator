<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;

class ExchangeRate
{
    public function __construct(
        private Client $client,
        private string $baseUri,
    ) {
    }

    public function get(string $currency): float
    {
        $response = $this->client->request('GET', $this->baseUri);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Error fetching exchange rates');
        }
        $rates = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $rates['rates'][$currency] ?? 0;
    }
}
