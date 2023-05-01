<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class BadRequestErrorException extends HttpException
{
    public function __construct(string $class, int $code, string $url, string $content)
    {
        parent::__construct(
            $code,
            sprintf("Bad request %d error in %s on request %s with content %s", $code, $class, $url, $content)
        );
    }
}