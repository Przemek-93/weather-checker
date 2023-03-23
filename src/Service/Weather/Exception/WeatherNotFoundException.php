<?php

declare(strict_types=1);

namespace App\Service\Weather\Exception;

use Exception;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class WeatherNotFoundException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = Response::HTTP_BAD_REQUEST,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            'Something went wrong while trying to get weather data. ' . $message,
            $code,
            $previous
        );
    }
}
