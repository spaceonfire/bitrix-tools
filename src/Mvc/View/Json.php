<?php

namespace spaceonfire\BitrixTools\Mvc\View;

use Bitrix\Main;

/**
 * JSON MVC view
 */
class Json extends Prototype
{
	/**
	 * Создает новый MVC JSON view
	 *
	 * @noinspection PhpMissingParentConstructorInspection MagicMethodsValidityInspection
	 * @param mixed $data Данные view
	 * @return void
	 */
	public function __construct($data = [])
	{
		$this->data = $data;
	}

	/**
	 * Отсылает http-заголовки для view
	 *
	 * @return void
	 */
	public function sendHeaders(): void
	{
		header('Content-type: application/json');
	}

	/**
	 * Формирует view
	 *
	 * @return string
	 */
	public function render(): string
	{
		return json_encode(
			defined('SITE_CHARSET') && SITE_CHARSET !== 'UTF-8' ?
				Main\Text\Encoding::convertEncoding($this->data, 'UTF-8', SITE_CHARSET) :
				$this->data
		);
	}
}
