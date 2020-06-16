<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\Components;

use InvalidArgumentException;
use Throwable;

class InvalidComponentParameterException extends InvalidArgumentException
{
    /**
     * InvalidComponentParameterException constructor.
     * @param Throwable[] $exceptions
     */
    public function __construct(Throwable ...$exceptions)
    {
        $errorMessages = array_reduce($exceptions, static function (string $accum, Throwable $e): string {
            return $accum . PHP_EOL . '- ' . $e->getMessage();
        }, '');
        $errorMessages = trim($errorMessages);

        parent::__construct('Component parameters invalid: ' . PHP_EOL . PHP_EOL . $errorMessages);
    }
}
