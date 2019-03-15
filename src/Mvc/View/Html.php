<?php

namespace spaceonfire\BitrixTools\Mvc\View;

/**
 * HTML MVC view
 */
class Html extends Prototype
{
	/**
	 * Создает новый MVC HTML view
	 *
	 * @noinspection PhpMissingParentConstructorInspection MagicMethodsValidityInspection
	 * @param string $data HTML текст
	 * @return void
	 */
	public function __construct($data = '')
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
		header('Content-type: text/html; charset=' . SITE_CHARSET);
	}

	/**
	 * Формирует view
	 *
	 * @return string
	 */
	public function render(): string
	{
		return is_array($this->data) ? implode('', $this->data) : (string)$this->data;
	}
}
