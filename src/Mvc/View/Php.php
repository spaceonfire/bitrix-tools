<?php

namespace spaceonfire\BitrixTools\Mvc\View;

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
		if (!is_file($this->baseDir . $this->name)) {
			throw new \Exception(sprintf('View "%s" isn\'t found.', $this->name));
		}

		ob_start();
		$result = &$this->data;
		require $this->baseDir . $this->name;
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
