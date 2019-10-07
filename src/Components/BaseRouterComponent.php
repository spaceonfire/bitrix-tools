<?php

namespace spaceonfire\BitrixTools\Components;

use CBitrixComponent;
use CComponentEngine;
use Throwable;

/**
 * Комплексный компонент (роутер)
 * @package spaceonfire\BitrixTools\Components
 */
abstract class BaseRouterComponent extends CBitrixComponent
{
	use CommonComponentTrait {
		CommonComponentTrait::executeMain as executeMainTrait;
	}

	/**
	 * @var array Настройки путей шаблонов по-умолчанию
	 */
	protected $defaultUrlTemplates404;
	/**
	 * @var array Переменные компонента, используемые в шаблонах URL
	 */
	protected $componentVariables;
	/**
	 * @var string Шаблон страницы по-умолчанию
	 */
	protected $defaultPage = 'list';
	/**
	 * @var string Шаблон страницы по-умолчанию в режиме ЧПУ
	 */
	protected $defaultSefPage = 'list';
	/**
	 * @var string Параметр запроса для поиска
	 */
	protected $seachQueryParam = 'q';
	/**
	 * @var string Значение параметра `SEF_FOLDER`
	 */
	protected $sefFolder;

	protected $urlTemplates;

	protected $variables;

	protected $variableAliases;

	/**
	 * Устанавливает параметры по-умолчанию для ЧПУ
	 */
	protected function setSefDefaultParams(): void
	{
		$this->defaultUrlTemplates404 = [
			'list' => '',
			'detail' => '#ELEMENT_ID#/'
		];

		$this->componentVariables = ['ELEMENT_ID'];
	}

	/**
	 * Проверяет был ли выполнен поисковой запрос
	 * @return bool
	 */
	protected function isSearchRequest(): bool
	{
		return $this->templatePage !== 'detail' && $this->request[$this->seachQueryParam] !== '';
	}

	/**
	 * Устанавливает тип запрошенной страницы и создает переменные из шаблонов URL
	 */
	protected function setPage(): void
	{
		$urlTemplates = [];

		if ($this->arParams['SEF_MODE'] === 'Y') {
			$variables = [];

			$urlTemplates = CComponentEngine::MakeComponentUrlTemplates(
				$this->defaultUrlTemplates404,
				$this->arParams['SEF_URL_TEMPLATES']
			);

			$variableAliases = CComponentEngine::MakeComponentVariableAliases(
				$this->defaultUrlTemplates404,
				$this->arParams['VARIABLE_ALIASES']
			);

			$this->templatePage = CComponentEngine::ParseComponentPath(
				$this->arParams['SEF_FOLDER'],
				$urlTemplates,
				$variables
			);

			if (!$this->templatePage) {
				if ($this->arParams['SET_404'] === 'Y') {
					$folder404 = str_replace('\\', '/', $this->arParams['SEF_FOLDER']);

					if ($folder404 !== '/') {
						$folder404 = '/' . trim($folder404, "/ \t\n\r\0\x0B") . '/';
					}

					if (substr($folder404, -1) === '/') {
						$folder404 .= 'index.php';
					}

					if ($folder404 !== $this->request->getRequestedPage()) {
						$this->return404();
					}
				}

				$this->templatePage = $this->defaultSefPage;
			}

			if ($this->arParams['USE_SEARCH'] === 'Y' && $this->isSearchRequest()) {
				$this->templatePage = 'search';
			}

			CComponentEngine::InitComponentVariables(
				$this->templatePage,
				$this->componentVariables,
				$variableAliases,
				$variables
			);
		} else {
			$this->templatePage = $this->defaultPage;
		}

		$this->sefFolder = $this->arParams['SEF_FOLDER'];
		$this->urlTemplates = $urlTemplates;
		$this->variables = $variables ?? [];
		$this->variableAliases = $variableAliases ?? [];
	}

	/**
	 * Основная логика компонента.
	 */
	protected function executeMain()
	{
		$this->executeMainTrait();
		$this->arResult['FOLDER'] = $this->sefFolder;
		$this->arResult['URL_TEMPLATES'] = $this->urlTemplates;
		$this->arResult['VARIABLES'] = $this->variables;
		$this->arResult['ALIASES'] = $this->variableAliases;
	}

	/**
	 * Универсальный флоу выполнения компонента
	 * @throws Throwable
	 */
	final public function run()
	{
		$this->includeModules();
		$this->init();
		$this->checkAutomaticParams();
		$this->startAjax();

		$this->setSefDefaultParams();
		$this->setPage();
		$this->executeProlog();

		$this->executeMain();
		$this->render();

		$this->executeEpilog();
		$this->stopAjax();
	}

	/**
	 * Выполнение компонента
	 */
	public function executeComponent()
	{
		try {
			$this->run();
		} catch (Throwable $e) {
			$this->catchError($e);
		}
	}
}
