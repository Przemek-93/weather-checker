<?php

declare(strict_types=1);

namespace App\Command\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateUserFailedException extends Exception
{
    public const DEFAULT_HTTP_STATUS = Response::HTTP_INTERNAL_SERVER_ERROR;

    public const DEFAULT_MESSAGE = 'Something went wrong while trying to create user by CLI command.';

    public function __construct(
        string $message = '',
        int $status = self::DEFAULT_HTTP_STATUS
    ) {
        parent::__construct(self::DEFAULT_MESSAGE . ' ' . $message, $status);
    }
}
