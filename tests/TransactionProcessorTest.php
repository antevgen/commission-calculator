<?php

declare(strict_types=1);

namespace Tests;

use App\Parsers\TransactionJsonParser;
use App\Services\BinInfo;
use App\Services\ExchangeRate;
use App\TransactionProcessor;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JsonException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TransactionProcessorTest extends TestCase
{
    private TransactionProcessor $processor;
    private MockObject|BinInfo $binInfoMock;

    protected function setUp(): void
    {
        $exchangeRateMock = new MockHandler([
            new Response(200, [], '{"rates":{"EUR":1,"USD":1.075604,"JPY":169.055801,"GBP":0.846339}}')
        ]);

        $exchangeRateHandler = HandlerStack::create($exchangeRateMock);

        $this->binInfoMock = $this->createMock(BinInfo::class);
        $exchangeRateClient = new Client(['handler' => $exchangeRateHandler]);

        $exchangeRateService = new ExchangeRate($exchangeRateClient, 'https://api.exchangeratesapi.io/latest');
        $transactionParser = new TransactionJsonParser();

        $this->processor = new TransactionProcessor($this->binInfoMock, $exchangeRateService, $transactionParser);
    }


    public static function transactionsData(): array
    {
        return [
            [
                '{"bin": "45717360", "amount": 100.00, "currency": "EUR"}',
                ['country' => ['alpha2' => 'DK']],
                1,
            ],
            [
                '{"bin":"516793","amount":"50.00","currency":"USD"}',
                ['country' => ['alpha2' => 'US']],
                0.93,
            ],
            [
                '{"bin":"45417360","amount":"10000.00","currency":"JPY"}',
                ['country' => ['alpha2' => 'JPN']],
                1.19,
            ],
            [
                '{"bin":"41417360","amount":"130.00","currency":"USD"}',
                ['country' => ['alpha2' => 'CAD']],
                2.42,
            ],
            [
                '{"bin":"4745030","amount":"2000.00","currency":"GBP"}',
                ['country' => ['alpha2' => 'GB']],
                47.27,
            ],
        ];
    }

    #[DataProvider('transactionsData')]
    public function testProcessTransactions(
        string $inputContent,
        array $expectedBinInfo,
        float  $expectedResult
    ): void {
        $inputFile = 'tests/test_input.txt';
        file_put_contents($inputFile, $inputContent . PHP_EOL);

        $this->binInfoMock->expects($this->once())
            ->method('get')
            ->willReturn($expectedBinInfo);

        $transactions = $this->processor->processTransactions($inputFile);
        $this->assertEquals($expectedResult, implode("\n", $transactions->pluck('commission')));

        unlink($inputFile);
    }

    public function testInvalidJson(): void
    {
        $inputFile = 'tests/test_invalid_json.txt';
        file_put_contents($inputFile, '{"bin": "45717360", "amount": 100.00, "currency": "EUR"');

        $this->expectException(JsonException::class);
        $this->processor->processTransactions($inputFile);

        unlink($inputFile);
    }

    public function testMissingFields(): void
    {
        $inputFile = 'tests/test_missing_fields.txt';
        file_put_contents($inputFile, '{"bin": "45717360", "amount": 100.00}' . PHP_EOL);

        $this->expectOutputString("Missing required fields.\n");
        $this->processor->processTransactions($inputFile);

        unlink($inputFile);
    }
}
