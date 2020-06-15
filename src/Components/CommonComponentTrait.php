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
use InvalidArgumentException;
use Narrowspark\HttpStatus\Exception\NotFoundException;
use RuntimeException;
use spaceonfire\BitrixTools\Common as CommonTools;
use spaceonfire\BitrixTools\Components\Property\ComponentPropertiesTrait;
use spaceonfire\BitrixTools\HttpStatusTools;
use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\DisjunctionType;
use spaceonfire\Type\Type;
use Throwable;

Loc::loadMessages(__FILE__);

/**
 * Общая логика для базовых компонентов
 * @package spaceonfire\BitrixTools\Components
 */
trait CommonComponentTrait
{
    /**
     * @var string
     */
    private $id;
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
     * @deprecated Переопределяйте метод getParamsTypes() для указания типов параметров компонента
     */
    protected $checkParams = [];

    /**
     * Возвращает идентификатор компонента
     * @return string
     */
    public function getId(): string
    {
        if ($this->id === null) {
            $this->id = $this->randString();
        }
        return $this->id;
    }

    /**
     * Загружает файлы переводов компонента (component.php и class.php)
     */
    public function onIncludeComponentLang(): void
    {
        parent::onIncludeComponentLang();
        $this->includeComponentLang('class.php');
    }

    /**
     * Подготовка параметров компонента
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams(array $arParams): array
    {
        try {
            foreach ($this->getParamsTypes() as $param => $type) {
                $value = $arParams[$param] ?? null;

                if (!$type->check($value)) {
                    throw new InvalidArgumentException(sprintf(
                        'Value of "%s" param must be of type "%s". Got: "%s"',
                        $param,
                        (string)$type,
                        gettype($value)
                    ));
                }

                if ($type instanceof BuiltinType) {
                    $value = $type->cast($value);
                }

                if ((string)$type === 'string') {
                    $value = htmlspecialchars(trim($value));
                }

                $arParams[$param] = $value;
            }

            // There should be more options for params checking, validation as example

            return $arParams;
        } catch (InvalidArgumentException $exception) {
            if (!$this->canShowExceptionMessage($exception)) {
                throw new NotFoundException($exception->getMessage());
            }

            throw $exception;
        }
    }

    /**
     * Загружает модули 1С-Битрикс.
     */
    final protected function includeModules(): void
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
        if (in_array(ComponentPropertiesTrait::class, class_uses($this), true)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->initPropertiesBag();
        }
    }

    /**
     * Возвращает массив типов для проверки параметров компонента
     * @return Type[]
     */
    protected function getParamsTypes(): array
    {
        $result = [];

        if (!empty($this->checkParams)) {
            trigger_error(sprintf(
                'Error in component %s: property "checkParams" is deprecated. Use getParamsTypes() method instead.',
                $this->getName()
            ), E_USER_DEPRECATED);
        }

        foreach ($this->checkParams as $key => $options) {
            switch ($options['type']) {
                case 'int':
                    $type = new BuiltinType(BuiltinType::INT, false);
                    break;

                case 'float':
                    $type = new BuiltinType(BuiltinType::FLOAT, false);
                    break;

                case 'string':
                    $type = new BuiltinType(BuiltinType::STRING, false);
                    break;

                case 'array':
                    $type = new BuiltinType(BuiltinType::ARRAY);
                    break;

                default:
                    continue 2;
                    break;
            }

            if (isset($options['error']) && $options['error'] === false) {
                $type = new DisjunctionType([$type, new BuiltinType(BuiltinType::NULL)]);
            }

            $result[$key] = $type;
        }

        // I should probably add default component params like CACHE_TYPE, CACHE_TIME etc.

        return $result;
    }

    /**
     * Возвращает значение параметра родительского компонента
     * @param string $paramName
     * @return mixed|null
     */
    final protected function getParentParam(string $paramName)
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

        if ($this->arParams['AJAX_TEMPLATE_PAGE'] !== '') {
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
    private function startCache(): bool
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
    private function writeCache(): void
    {
        $this->endResultCache();
    }

    /**
     * Сброс кэширования.
     */
    private function abortCache(): void
    {
        $this->abortResultCache();
    }

    /**
     * Основная логика компонента.
     * Результат работы метода будет закэширован.
     */
    protected function executeMain(): void
    {
        if ($this->arParams['AJAX_PARAM_NAME'] !== '' && $this->arParams['AJAX_COMPONENT_ID'] !== '') {
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
    final protected function return404(?Throwable $throwable = null): void
    {
        throw new NotFoundException($throwable ? $throwable->getMessage() : null);
    }

    /**
     * Вызывается при возникновении ошибки
     *
     * Сбрасывает кэш, показывает сообщение об ошибке (в общем виде для пользователей и детально для админов),
     * пишет ошибку в лог Битрикса
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
     */
    public static function registerCacheTag(string $tag): void
    {
        if (!$tag) {
            return;
        }

        try {
            Application::getInstance()->getTaggedCache()->registerTag($tag);
        } catch (Main\SystemException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Добавляет дополнительный ID для кэша
     * @param mixed $id
     */
    final protected function addCacheAdditionalId($id): void
    {
        $this->cacheAdditionalId[] = $id;
    }

    /**
     * Вызывает событие, специфичное для компонента
     * @param string $type Тип события. Имя класса компонента будет добавлено в виде префикса.
     * @param array $params Параметры события. Параметр `component` будет добавлен автоматически
     * @param null|string|string[] $filter Фильтр события
     * @return Event
     */
    final protected function triggerEvent(string $type, array $params = [], $filter = null): Event
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
