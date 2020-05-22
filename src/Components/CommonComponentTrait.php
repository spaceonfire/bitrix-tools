<?php

namespace spaceonfire\BitrixTools\Components;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Diag\ExceptionHandlerLog;
use Bitrix\Main\Event;
use Bitrix\Main\Localization\Loc;
use CAjax;
use CBitrixComponent;
use CMain;
use Narrowspark\HttpStatus\Exception\NotFoundException;
use spaceonfire\BitrixTools\Common as CommonTools;
use spaceonfire\BitrixTools\HttpStatusTools;
use Throwable;

Loc::loadMessages(__FILE__);

/**
 * Общая логика для базовых компонентов
 * @package spaceonfire\BitrixTools\Components
 */
trait CommonComponentTrait
{
    /**
     * @var array Массив модулей, которые необходимо загрузить перед подключением компонента
     */
    protected $needModules = [];

    /**
     * @var array Массив дополнительных ID кэша
     */
    private $cacheAdditionalId = [];

    /**
     * @var string Директория кэша
     */
    protected $cacheDir = false;

    /**
     * @var string Salt for component ID for AJAX request
     */
    protected $ajaxComponentIdSalt;

    /**
     * @var string Название страницы шаблона (для компонента-роутера)
     */
    protected $templatePage;

    /**
     * @var array Настройки для проверки параметров компонента
     * @example $checkParams = array('IBLOCK_TYPE' => array('type' => 'string'), 'ELEMENT_ID' => array('type' => 'int',
     *     'error' => '404'));
     */
    protected $checkParams = [];

    /**
     * Загружает файлы переводов компонента (component.php и class.php)
     */
    public function onIncludeComponentLang(): void
    {
        parent::onIncludeComponentLang();
        $this->includeComponentLang('class.php');
    }

    /**
     * Загружает модули 1С-Битрикс.
     * @throws Main\LoaderException
     */
    public function includeModules(): void
    {
        if (!is_array($this->needModules) || empty($this->needModules)) {
            return;
        }

        CommonTools::loadModules($this->needModules);
    }

    /**
     * Инициализация компонента.
     * Метод вызывается после вызова конструктора и подключения необходимых модулей.
     * Служит для выполнения дополнительных настроек.
     */
    protected function init(): void
    {
    }

    /**
     * @throws Throwable
     */
    private function checkAutomaticParams(): void
    {
        try {
            foreach ($this->checkParams as $key => $param) {
                if ($param['error'] === false) {
                    continue;
                }

                switch ($param['type']) {
                    case 'int':
                        if (!is_numeric($this->arParams[$key])) {
                            throw new Main\ArgumentTypeException($key, 'integer');
                        }

                        $this->arParams[$key] = (int)$this->arParams[$key];
                        break;

                    case 'float':
                        if (!is_numeric($this->arParams[$key])) {
                            throw new Main\ArgumentTypeException($key, 'float');
                        }

                        $this->arParams[$key] = (float)$this->arParams[$key];
                        break;

                    case 'string':
                        $value = htmlspecialchars(trim($this->arParams[$key]));

                        if (strlen($value) <= 0) {
                            throw new Main\ArgumentNullException($key);
                        }

                        $this->arParams[$key] = $value;
                        break;

                    case 'array':
                        if (!is_array($this->arParams[$key])) {
                            throw new Main\ArgumentTypeException($key, 'array');
                        }
                        break;

                    default:
                        throw new Main\ArgumentTypeException($key);
                        break;
                }
            }
        } catch (Main\ArgumentException $exception) {
            if ($this->checkParams[$exception->getParameter()]['error'] === '404') {
                $this->return404($exception);
            } else {
                throw $exception;
            }
        }
    }

    /**
     * Возвращает значение параметра родительского компонента
     * @param string $paramName
     * @return mixed|null
     */
    protected function getParentParam(string $paramName)
    {
        if (!($parent = $this->getParent())) {
            return null;
        }

        if (!isset($parent->arParams[$paramName])) {
            return null;
        }

        return $parent->arParams[$paramName];
    }

    /**
     * Рестарт буфера для AJAX запроса
     */
    private function startAjax(): void
    {
        if ($this->arParams['USE_AJAX'] !== 'Y' || !$this->isAjax()) {
            return;
        }

        if (strlen($this->arParams['AJAX_PARAM_NAME']) <= 0) {
            $this->arParams['AJAX_PARAM_NAME'] = 'compid';
        }

        if (strlen($this->arParams['AJAX_COMPONENT_ID']) <= 0) {
            $this->arParams['AJAX_COMPONENT_ID'] = CAjax::GetComponentID($this->getName(), $this->getTemplateName(), $this->ajaxComponentIdSalt);
        }

        global $APPLICATION;

        if ($this->arParams['AJAX_HEAD_RELOAD'] === 'Y') {
            $APPLICATION->ShowAjaxHead();
        } else {
            $APPLICATION->RestartBuffer();
        }

        if (strlen($this->arParams['AJAX_TEMPLATE_PAGE']) > 0) {
            $this->templatePage = basename($this->arParams['AJAX_TEMPLATE_PAGE']);
        }
    }

    /**
     * Выполняется до получения результатов. Не кэшируется
     */
    protected function executeProlog(): void
    {
    }

    /**
     * Инициализация кэширования
     * @return bool
     */
    public function startCache(): bool
    {
        global $USER;

        if ($this->arParams['CACHE_TYPE'] && $this->arParams['CACHE_TYPE'] !== 'N' && $this->arParams['CACHE_TIME'] > 0) {
            if ($this->templatePage) {
                $this->addCacheAdditionalId($this->templatePage);
            }

            if ($this->arParams['CACHE_GROUPS'] === 'Y') {
                $this->addCacheAdditionalId($USER->GetGroups());
            }

            if ($this->startResultCache($this->arParams['CACHE_TIME'], $this->cacheAdditionalId, $this->cacheDir)) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Записывает результат кэширования на диск.
     */
    public function writeCache(): void
    {
        $this->endResultCache();
    }

    /**
     * Сброс кэширования.
     */
    public function abortCache(): void
    {
        $this->abortResultCache();
    }

    /**
     * Основная логика компонента.
     * Результат работы метода будет закэширован.
     */
    protected function executeMain(): void
    {
        if (strlen($this->arParams['AJAX_PARAM_NAME']) > 0 && strlen($this->arParams['AJAX_COMPONENT_ID']) > 0) {
            $this->arResult['AJAX_REQUEST_PARAMS'] = $this->arParams['AJAX_PARAM_NAME'] . '=' . $this->arParams['AJAX_COMPONENT_ID'];

            $this->setResultCacheKeys(['AJAX_REQUEST_PARAMS']);
        }
    }

    /**
     * Выполняется после получения результатов. Не кэшируется
     */
    protected function executeEpilog(): void
    {
    }

    /**
     * Заканчивает выполнение скрипта для AJAX запроса
     */
    private function stopAjax(): void
    {
        if ($this->arParams['USE_AJAX'] !== 'Y' || !$this->isAjax()) {
            return;
        }

        CMain::FinalActions();
        die();
    }

    /**
     * Рендеринг шаблона компонента
     */
    public function render(): void
    {
        $this->includeComponentTemplate($this->templatePage);
    }

    /**
     * Выбрасывает NotFoundException
     * @param Throwable|null $throwable Исходное исключение. При наличии будет использовано его сообщение об ошибке
     * @throws NotFoundException
     */
    public function return404(?Throwable $throwable = null): void
    {
        throw new NotFoundException($throwable ? $throwable->getMessage() : null);
    }

    /**
     * Вызывается при возникновении ошибки
     *
     * Сбрасывает кэш, показывает сообщение об ошибке (в общем виде для пользователей и детально
     * для админов), пишет ошибку в лог Битрикса
     *
     * @param Throwable $exception
     */
    protected function catchError(Throwable $exception): void
    {
        $this->abortCache();

        HttpStatusTools::catchError($exception);

        $errorMessage = $this->canShowExceptionMessage($exception)
            ? $exception->getMessage()
            : Loc::getMessage('COMPONENT_CATCH_EXCEPTION');

        $this->renderExceptionMessage($errorMessage);

        if ($this->canShowExceptionTrace($exception)) {
            $this->renderExceptionTrace($exception);
        }

        try {
            Application::getInstance()->getExceptionHandler()
                ->writeToLog($exception, ExceptionHandlerLog::CAUGHT_EXCEPTION);
        } catch (Throwable $e) {
        }
    }

    /**
     * Определяет можно ли показать сообщение исключения
     * @param Throwable $exception
     * @return bool
     */
    protected function canShowExceptionMessage(Throwable $exception): bool
    {
        global $USER;
        return $USER->IsAdmin();
    }

    /**
     * Определяет можно ли показать трейс исключения
     * @param Throwable $exception
     * @return bool
     */
    protected function canShowExceptionTrace(Throwable $exception): bool
    {
        global $USER;

        $exceptionHandling = Configuration::getValue('exception_handling') ?? ['debug' => false];
        $isDebugEnabled = (bool)$exceptionHandling['debug'];

        return $USER->IsAdmin() && $isDebugEnabled;
    }

    /**
     * Отображает сообщение об ошибке
     * @param string $message
     */
    protected function renderExceptionMessage(string $message): void
    {
        ShowError($message);
    }

    /**
     * Отображает трейс ошибки
     * @param Throwable $throwable
     */
    protected function renderExceptionTrace(Throwable $throwable): void
    {
        echo nl2br($throwable->getTraceAsString());
    }

    /**
     * Проверяет отправлен ли запрос через AJAX
     * @return bool
     */
    public function isAjax(): bool
    {
        /** @var CBitrixComponent $this */
        return $this->request->isAjaxRequest();
    }

    /**
     * Регистрирует тэг в кэше
     * @param string $tag
     * @throws Main\SystemException
     */
    public static function registerCacheTag(string $tag): void
    {
        if ($tag) {
            Application::getInstance()->getTaggedCache()->registerTag($tag);
        }
    }

    /**
     * Добавляет дополнительный ID для кэша
     * @param mixed $id
     */
    public function addCacheAdditionalId($id): void
    {
        $this->cacheAdditionalId[] = $id;
    }

    /**
     * Вызывает событие, специфичное для компонента
     * @param string $type Тип события. Имя класса компонента будет добавлено ввиде префикса.
     * @param array $params Параметры события. Параметр `component` будет добавлен автоматически
     * @param null|string|string[] $filter Фильтр события
     * @return Event
     */
    public function triggerEvent(string $type, array $params = [], $filter = null): Event
    {
        $event = new Event(
            CommonTools::getModuleIdByFqn(static::class),
            static::class . '::' . $type,
            ['component' => $this] + $params,
            $filter
        );
        $event->send();
        return $event;
    }
}
