<?php

namespace spaceonfire\BitrixTools\Mvc\View;

/**
 * Абстрактный MVC view
 */
class Prototype
{
	/**
	 * Каталог по умолчанию для файлов view
	 *
	 * @var string
	 */
	protected $baseDir = '';

	/**
	 * Имя view
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Данные view
	 *
	 * @var mixed
	 */
	protected $data = [];

	/**
	 * Создает новый MVC view
	 *
	 * @param string $name Название шаблона view
	 * @param mixed $data Данные view
	 * @param string $baseDir
	 */
	public function __construct($name = '', $data = [], $baseDir = '')
	{
		$this->name = $name;
		$this->data = $data;

		if ($baseDir) {
			$this->setBaseDir($baseDir);
		}
	}

	/**
	 * Отсылает http-заголовки для view
	 *
	 * @return void
	 */
	public function sendHeaders(): void
	{
	}

	/**
	 * Формирует view
	 *
	 * @noinspection PhpDocMissingThrowsInspection
	 * @return string
	 */
	public function render(): string
	{
		throw new \Exception("Abstract view can't be rendered.");
	}

	/**
	 * Устанавливает данные
	 *
	 * @param mixed $data Данные
	 * @return void
	 */
	public function setData($data): void
	{
		$this->data = $data;
	}

	/**
	 * Устанавливает базовый каталог
	 *
	 * @param string $dir Базовый каталог
	 * @return void
	 */
	public function setBaseDir($dir): void
	{
		$this->baseDir = $dir;
	}
}
