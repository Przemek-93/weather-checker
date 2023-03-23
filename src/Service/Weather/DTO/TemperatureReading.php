<?php

declare(strict_types=1);

namespace App\Service\Weather\DTO;

use DateTime;
use JsonSerializable;

class TemperatureReading implements JsonSerializable
{
    public function __construct(
        public readonly string $station,
        public readonly DateTime $date,
        public readonly float $temp
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'stacja' => ucfirst($this->station),
            'data_pomiaru' => $this->date->format('Y-m-d H:i:s'),
            'temperatura' => $this->temp
        ];
    }
}
