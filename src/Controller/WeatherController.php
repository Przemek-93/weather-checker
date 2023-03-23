<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Weather\Fetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

final class WeatherController extends AbstractController
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
            $weatherReading = $this->weatherFetcher->fetchTemperatureByStation($station);
        } catch (Throwable $throwable) {
            $this->addFlash('error', 'Error: ' . $throwable->getMessage());
        }

        return $this->render(
            'weather.html.twig',
            [
                'reading' => $weatherReading ?? null,
                'station' => $station
            ]
        );
    }
}
