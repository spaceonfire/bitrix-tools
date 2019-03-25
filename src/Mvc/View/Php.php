<?php

namespace spaceonfire\BitrixTools\Mvc\View;

use Bitrix\Main;

/**
 * PHP MVC view
 */
class Php extends Prototype
{
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
	 * @throws \Exception
	 */
	public function render(): string
	{
		$path = $this->getPath();
		if (!is_file($path)) {
			throw new Main\IO\FileNotFoundException($path);
		}

		Main\Localization\Loc::loadMessages($path);

		ob_start();
		$result = &$this->data;
		require $path;
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Выводит HTML в безопасном виде
	 *
	 * @param string $data Выводимые данные
	 * @return string
	 */
	public function escape($data): string
	{
		return htmlspecialchars($data);
	}
}
