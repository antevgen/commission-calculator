<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Services\ExchangeRate;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ExchangeRateTest extends TestCase
{
    private ExchangeRate $service;

    private Client|MockObject $clientMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);

        $this->service = new ExchangeRate($this->clientMock, 'https://api.exchangeratesapi.io/latest');
    }

    public function testGetExchangeRate(): void
    {
        $this->clientMock->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], '{"rates":{"USD":1.2,"JPY":130.0,"GBP":0.85}}'));

        $rate = $this->service->get('USD');

        $this->assertEquals(1.2, $rate);
    }

    public function testGetExchangeRateNotFound(): void
    {
        $this->clientMock->expects($this->once())
            ->method('request')
            ->willReturn(new Response(404, [], ''));

        $this->expectException(Exception::class);

        $this->service->get('USD');
    }
}
