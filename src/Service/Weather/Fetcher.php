<?php

declare(strict_types=1);

namespace App\Service\Weather;

use App\Service\Weather\DTO\FullDataReading;
use App\Service\Weather\DTO\TemperatureReading;
use App\Service\Weather\Exception\WeatherNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class Fetcher
{
    public function __construct(
        protected HttpClientInterface $imgwClient,
        protected ResponseToReadingTransformer $transformer,
        protected LoggerInterface $logger
    ) {
    }

    public function fetchTemperatureByStation(string $station): TemperatureReading
    {
        try {
            $station = $this->extractSpecialCharacters($station);
            $response = $this->imgwClient->request(
                'GET',
                '/api/data/synop/station/' . $station
            );

            return $this->transformer->responseToTemperature($response->toArray());
        } catch (Throwable $throwable) {
            if ($throwable instanceof HttpExceptionInterface) {
                $error = $throwable->getResponse()->toArray(false)['message'];
            }

            $message = sprintf(
                'Cannot fetch data for given station: [%s], error: [%s]',
                $station,
                $error ?? $throwable->getMessage()
            );
            $this->logger->error($message);

            throw new WeatherNotFoundException(
                $message,
                $throwable->getCode()
            );
        }
    }

    public function fetchFullDataByStation(string $station): FullDataReading
    {
        try {
            $station = $this->extractSpecialCharacters($station);
            $response = $this->imgwClient->request(
                'GET',
                '/api/data/synop/station/' . $station
            );

            return $this->transformer->responseToFullData($response->toArray());
        } catch (Throwable $throwable) {
            if ($throwable instanceof HttpExceptionInterface) {
                $error = $throwable->getResponse()->toArray(false)['message'];
            }

            $message = sprintf(
                'Cannot fetch data for given station: [%s], error: [%s]',
                $station,
                $error ?? $throwable->getMessage()
            );
            $this->logger->error($message);

            throw new WeatherNotFoundException(
                $message,
                $throwable->getCode()
            );
        }
    }

    protected function extractSpecialCharacters(string $station): string
    {
        return iconv(
            'UTF-8',
            'ASCII//TRANSLIT',
            strtolower($station)
        );
    }
}
