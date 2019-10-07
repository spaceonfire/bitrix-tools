<?php

namespace spaceonfire\BitrixTools;

use CHTTP;
use Narrowspark\HttpStatus\Contract\Exception\HttpException as HttpExceptionContract;
use Narrowspark\HttpStatus\Exception\InternalServerErrorException;
use Narrowspark\HttpStatus\HttpStatus;
use Throwable;

class HttpStatusTools extends HttpStatus
{
	/**
	 * Устанавливает статус ответа и константу ошибки
	 * @param Throwable $throwable
	 */
	public static function catchError(Throwable $throwable)
	{
		if ($throwable instanceof HttpExceptionContract) {
			$httpError = $throwable;
		} else {
			$httpError = new InternalServerErrorException($throwable->getMessage());
		}

		// Define error constant
		$statusCode = $httpError->getStatusCode();
		defined('ERROR_' . $statusCode) or define('ERROR_' . $statusCode, 'Y');

		// Set status
		CHTTP::SetStatus($statusCode . ' ' . static::getReasonPhrase($statusCode));
	}
}
