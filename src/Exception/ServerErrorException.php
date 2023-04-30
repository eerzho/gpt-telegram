<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ServerErrorException extends HttpException
{
    public function __construct(string $class, int $code, string $url, string $content)
    {
        parent::__construct(
            $code,
            sprintf("Server error in %s on request %s with content %s", $class, $url, $content)
        );
    }
}