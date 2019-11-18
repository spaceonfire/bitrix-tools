<?php

namespace spaceonfire\BitrixTools\Components;

use CBitrixComponent;
use Throwable;

/**
 * Базовый компонент
 * @package spaceonfire\BitrixTools\Components
 */
abstract class BaseComponent extends CBitrixComponent
{
	use CommonComponentTrait;

	/**
	 * @var bool Указывает необходимо ли кэшировать шаблон компонента (включено по-умолчанию)
	 */
	protected $cacheTemplate = true;

	/**
	 * Универсальный флоу выполнения компонента
	 * @throws Throwable
	 */
	final protected function run(): void
	{
		$this->includeModules();
		$this->init();
		$this->checkAutomaticParams();
		$this->startAjax();
		$this->executeProlog();

		if ($this->startCache()) {
			$this->executeMain();

			if ($this->cacheTemplate) {
				$this->render();
			}

			$this->writeCache();
		}

		if (!$this->cacheTemplate) {
			$this->render();
		}

		$this->executeEpilog();
		$this->stopAjax();
	}

	/**
	 * Выполнение компонента
	 * @return static возвращает объект компонента
	 */
	public function executeComponent()
	{
		try {
			$this->run();
		} catch (Throwable $e) {
			$this->catchError($e);
		}
		return $this;
	}
}
