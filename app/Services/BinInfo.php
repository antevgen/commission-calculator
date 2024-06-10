<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;

class BinInfo
{
    public function __construct(
        private Client $client,
        private string $baseUri,
    ) {
    }

    public function get(string $bin): ?array
    {
        $response = $this->client->request('GET', $this->baseUri . $bin);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Error fetching BIN info');
        }

        return json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
