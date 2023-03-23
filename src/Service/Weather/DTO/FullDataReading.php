<?php

declare(strict_types=1);

namespace App\Service\Weather\DTO;

use DateTime;
use JsonSerializable;

class FullDataReading implements JsonSerializable
{
    public function __construct(
        public readonly int $stationId,
        public readonly string $station,
        public readonly DateTime $date,
        public readonly float $temp,
        public readonly int $windSpeed,
        public readonly int $windDirection,
        public readonly float $humidity,
        public readonly float $totalPrecipitation,
        public readonly float $pressure,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id_stacji' => $this->stationId,
            'stacja' => ucfirst($this->station),
            'data_pomiaru' => $this->date->format('Y-m-d H:i:s'),
            'temperatura' => $this->temp,
            'predkosc_wiatru' => $this->windSpeed,
            'kierunek_wiatru' => $this->windDirection,
            'wilgotnosc_wzgledna' => $this->humidity,
            'suma_opadu' => $this->totalPrecipitation,
            'cisnienie' => $this->pressure,
        ];
    }
}
