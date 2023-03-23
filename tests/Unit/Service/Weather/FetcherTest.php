<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Weather;

use App\Service\Weather\Exception\WeatherNotFoundException;
use App\Service\Weather\Fetcher;
use App\Service\Weather\ResponseToReadingTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Iterator;

class FetcherTest extends WebTestCase
{
    protected HttpClientInterface $client;
    protected LoggerInterface $logger;
    protected ResponseInterface $response;
    protected ResponseToReadingTransformer $transformer;
    protected array $temperatureResult = [
        'stacja' => 'Warszawa',
        'temperatura' => 1.1,
        'data_pomiaru' => '2022-03-23',
        'godzina_pomiaru' => 14
    ];
    protected array $fullDataResult = [
        'id_stacji' => 1,
        'stacja' => 'Warszawa',
        'temperatura' => 1.1,
        'data_pomiaru' => '2022-03-23',
        'godzina_pomiaru' => 14,
        'predkosc_wiatru' => 1,
        'kierunek_wiatru' => 1,
        'wilgotnosc_wzgledna' => 1.1,
        'suma_opadu' => 1.1,
        'cisnienie' => 1.1
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $container = self::bootKernel(['environment' => 'test'])->getContainer();
        $this->client = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->transformer = $container->get(ResponseToReadingTransformer::class);
    }

    public function testFetchTemperatureByStationSuccessful(): void
    {
        $this->response
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($this->temperatureResult);
        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);
        $this->logger
            ->expects($this->never())
            ->method('error');

        $fetcher = new Fetcher(
            $this->client,
            $this->transformer,
            $this->logger
        );
        $reading = $fetcher->fetchTemperatureByStation('test');
        $this->assertSame($this->temperatureResult['stacja'], $reading->station);
        $this->assertSame((float)$this->temperatureResult['temperatura'], $reading->temp);
    }

    /**
     * @dataProvider provideFaultyTemperatureResponse
     */
    public function testFetchTemperatureByStationMissedResponseData(string $missedProp): void
    {
        unset($this->temperatureResult[$missedProp]);
        $this->response
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($this->temperatureResult);
        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);
        $this->logger
            ->expects($this->once())
            ->method('error');

        $fetcher = new Fetcher(
            $this->client,
            $this->transformer,
            $this->logger
        );

        $this->expectException(WeatherNotFoundException::class);
        $fetcher->fetchTemperatureByStation('test');
    }

    public function provideFaultyTemperatureResponse(): Iterator
    {
        yield ['stacja'];
        yield ['temperatura'];
        yield ['godzina_pomiaru'];
        yield ['data_pomiaru'];
    }

    public function testFetchFullDataByStationSuccessful(): void
    {
        $this->response
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($this->fullDataResult);
        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);
        $this->logger
            ->expects($this->never())
            ->method('error');

        $fetcher = new Fetcher(
            $this->client,
            $this->transformer,
            $this->logger
        );
        $reading = $fetcher->fetchFullDataByStation('test');
        $this->assertSame($this->fullDataResult['stacja'], $reading->station);
        $this->assertSame((int)$this->fullDataResult['predkosc_wiatru'], $reading->windSpeed);
        $this->assertSame((int)$this->fullDataResult['kierunek_wiatru'], $reading->windDirection);
        $this->assertSame((float)$this->fullDataResult['wilgotnosc_wzgledna'], $reading->humidity);
        $this->assertSame((float)$this->fullDataResult['suma_opadu'], $reading->totalPrecipitation);
        $this->assertSame((float)$this->fullDataResult['cisnienie'], $reading->pressure);
    }

    /**
     * @dataProvider provideFaultyFullDataResponse
     */
    public function testFetchFullDataByStationMissedResponseData(string $missedProp): void
    {
        unset($this->fullDataResult[$missedProp]);
        $this->response
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($this->fullDataResult);
        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);
        $this->logger
            ->expects($this->once())
            ->method('error');

        $fetcher = new Fetcher(
            $this->client,
            $this->transformer,
            $this->logger
        );

        $this->expectException(WeatherNotFoundException::class);
        $fetcher->fetchFullDataByStation('test');
    }

    public function provideFaultyFullDataResponse(): Iterator
    {
        yield ['stacja'];
        yield ['temperatura'];
        yield ['godzina_pomiaru'];
        yield ['data_pomiaru'];
        yield ['predkosc_wiatru'];
        yield ['kierunek_wiatru'];
        yield ['wilgotnosc_wzgledna'];
        yield ['cisnienie'];
    }
}
