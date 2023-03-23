<?php

declare(strict_types=1);

namespace App\Service\Weather;

use App\Service\Weather\DTO\FullDataReading;
use App\Service\Weather\DTO\TemperatureReading;
use DateTime;

class ResponseToReadingTransformer
{
    public function responseToTemperature(array $response): TemperatureReading
    {
        return new TemperatureReading(
            $response['stacja'],
            DateTime::createFromFormat(
                'Y-m-dH',
                $response['data_pomiaru'] . $response['godzina_pomiaru']
            ),
            (float) $response['temperatura']
        );
    }

    public function responseToFullData(array $response): FullDataReading
    {
        return new FullDataReading(
            (int) $response['id_stacji'],
            $response['stacja'],
            DateTime::createFromFormat(
                'Y-m-dH',
                $response['data_pomiaru'] . $response['godzina_pomiaru']
            ),
            (float) $response['temperatura'],
            (int) $response['predkosc_wiatru'],
            (int) $response['kierunek_wiatru'],
            (float) $response['wilgotnosc_wzgledna'],
            (float) $response['suma_opadu'],
            (float) $response['cisnienie'],
        );
    }
}
