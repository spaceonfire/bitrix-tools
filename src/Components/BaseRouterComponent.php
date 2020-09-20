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
     * Устанавливает параметры ЧПУ по-умолчанию
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
        return $this->request[$this->seachQueryParam] &&
            $this->request[$this->seachQueryParam] !== '' &&
            $this->templatePage !== 'detail';
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
    protected function executeMain(): void
    {
        $this->executeMainTrait();
        $this->arResult['FOLDER'] = $this->sefFolder;
        $this->arResult['URL_TEMPLATES'] = $this->urlTemplates;
        $this->arResult['VARIABLES'] = $this->variables;
        $this->arResult['ALIASES'] = $this->variableAliases;
    }

    /**
     * Универсальный порядок выполнения комплексного компонента
     */
    final public function run(): void
    {
        $this->includeModules();
        $this->init();
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
     * @return $this|mixed возвращает экземпляр текущего компонента
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

    /**
     * Проверяет, объявлен ли шаблон Url компонентом в `defaultUrlTemplates404`
     * @param string $templateName
     * @return bool
     */
    public function hasUrlTemplate(string $templateName): bool
    {
        return isset($this->defaultUrlTemplates404[$templateName]);
    }

    /**
     * Возвращает шаблон Url по названию
     * @param string $templateName
     * @return string|null
     */
    public function getUrlTemplate(string $templateName): ?string
    {
        if (!$this->hasUrlTemplate($templateName)) {
            return null;
        }

        return $this->arParams['SEF_URL_TEMPLATES'][$templateName] ??
            $this->defaultUrlTemplates404[$templateName];
    }

    /**
     * Собирает Url из шаблона
     * @param string $templateName
     * @param array $params
     * @return string|null
     */
    public function buildUrl(string $templateName, array $params = []): ?string
    {
        if (($template = $this->getUrlTemplate($templateName)) === null) {
            return null;
        }

        return $this->arParams['SEF_FOLDER'] . CComponentEngine::makePathFromTemplate($template, $params);
    }
}
