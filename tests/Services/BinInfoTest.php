<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Services\BinInfo;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BinInfoTest extends TestCase
{
    private BinInfo $service;

    private Client|MockObject $clientMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->service = new BinInfo($this->clientMock, 'https://lookup.binlist.net/');
    }

    public function testGetBinInfo(): void
    {
        $this->clientMock->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], '{"country":{"alpha2":"LT"}}'));

        $binInfo = $this->service->get('45717360');

        $this->assertEquals('LT', $binInfo['country']['alpha2']);
    }

    public function testGetBinInfoNotFound(): void
    {
        $this->clientMock->expects($this->once())
            ->method('request')
            ->willReturn(new Response(404, [], ''));

        $this->expectException(Exception::class);

        $this->service->get('45717360');
    }
}
