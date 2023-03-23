<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Weather\Fetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route(path: '/api')]
final class WeatherAPIController extends AbstractController
{
    public function __construct(
        private Fetcher $weatherFetcher
    ) {
    }

    #[Route(
        path: '/pogoda/{station}',
        requirements: ['station' => '[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+'],
        methods: ['GET']
    )]
    public function getTemperatureByStation(string $station): Response
    {
        try {
            return new JsonResponse(
                $this->weatherFetcher->fetchTemperatureByStation($station),
                Response::HTTP_OK
            );
        } catch (Throwable $throwable) {
            return new JsonResponse(
                ['error' => $throwable->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    #[Route(
        path: '/pogoda/{station}/full',
        requirements: ['station' => '[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+'],
        methods: ['GET']
    )]
    public function getFullDataByStation(string $station): Response
    {
        try {
            return new JsonResponse(
                $this->weatherFetcher->fetchFullDataByStation($station),
                Response::HTTP_OK
            );
        } catch (Throwable $throwable) {
            return new JsonResponse(
                ['error' => $throwable->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        }
    }
}
