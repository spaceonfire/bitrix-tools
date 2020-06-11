<?php

namespace spaceonfire\BitrixTools\Controllers;

use Bitrix\Main\Context;
use Bitrix\Main\HttpRequest;
use Narrowspark\HttpStatus\Exception\NotFoundException;
use spaceonfire\BitrixTools\ArrayTools;
use spaceonfire\BitrixTools\Views\JsonView;
use spaceonfire\BitrixTools\Views\ViewInterface;
use stdClass;
use Throwable;

class BaseController implements ControllerInterface
{
    /**
     * @var HttpRequest Запрос
     */
    protected $request;
    /**
     * @var ViewInterface|null Представление
     */
    protected $view;
    /**
     * @var boolean Вернуть возвращенные экшеном данные как есть, без признака success
     */
    protected $returnAsIs = false;
    /**
     * @var array Параметры
     */
    protected $params = [];

    /**
     * Создает новый контроллер
     */
    public function __construct()
    {
        $this->request = Context::getCurrent()->getRequest();
    }

    /**
     * "Фабрика" контроллеров
     * @param string $name Имя сущности
     * @param string $namespace Неймспейс класса
     * @return static
     * @throws NotFoundException
     */
    public static function factory($name, $namespace = __NAMESPACE__): ControllerInterface
    {
        $namespace = rtrim($namespace, '\\');

        if (strpos($name, '/') !== false) {
            $nameParts = explode('/', $name);
            $name = array_pop($nameParts);
            $namespace .= '\\' . implode('\\', array_map('ucfirst', $nameParts));
        }

        $name = preg_replace('/[^A-z0-9_]/', '', $name);

        $checkClasses = [$namespace . '\\' . ucfirst($name), $namespace . '\\' . ucfirst($name) . 'Controller'];

        foreach ($checkClasses as $className) {
            if (class_exists($className)) {
                return new $className();
            }
        }

        throw new NotFoundException(sprintf('Controller "%s" doesn\'t exists.', $name));
    }

    /**
     * Выполняет экшн контроллера
     * @param string $name Имя экшена
     * @throws NotFoundException
     */
    public function doAction($name): void
    {
        $name = preg_replace('/[^A-z0-9_]/', '', $name);
        $methodName = $name . 'Action';

        if (!method_exists($this, $methodName)) {
            throw new NotFoundException(sprintf('Action "%s" doesn\'t exists.', $name));
        }

        //JSON view by default
        $this->view = new JsonView();

        $response = new stdClass();
        $response->success = false;
        try {
            $response->data = $this->$methodName();
            $response->success = true;
        } catch (Throwable $e) {
            $response->code = $e->getCode();
            $response->message = $e->getMessage();
        }

        try {
            $this->view->setData($this->returnAsIs ? ($response->data ?? null) : $response);
            $this->view->sendHeaders();
            echo $this->view->render();
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Возвращает код, сгенерированный компонентом Битрикс
     * @param string $name Имя компонента
     * @param string $template Шаблон компонента
     * @param array $params Параметры компонента
     * @param mixed $componentResult Данные, возвращаемые компонентом
     * @return string
     * @see \CMain::IncludeComponent()
     */
    protected function getComponent($name, $template = '', $params = [], &$componentResult = null): string
    {
        ob_start();
        $componentResult = $GLOBALS['APPLICATION']->IncludeComponent($name, $template, $params);
        return ob_get_clean();
    }

    /**
     * Возвращает код, сгенерированный включаемой областью Битрикс
     * @param string $path Путь до включаемой области
     * @param array $params Массив параметров для подключаемого файла
     * @param array $functionParams Массив настроек данного метода
     * @return string
     * @see \CMain::IncludeFile()
     */
    protected function getIncludeArea($path, $params = [], $functionParams = []): string
    {
        ob_start();
        $GLOBALS['APPLICATION']->IncludeFile($path, $params, $functionParams);
        return ob_get_clean();
    }

    /**
     * Сеттер для свойства `params`
     * @param array $params
     * @param bool $merge
     */
    public function setParams(array $params, bool $merge = true): void
    {
        if ($this->params === null || !$merge) {
            $this->params = $params;
        } else {
            $this->params = ArrayTools::merge($this->params, $params);
        }
    }

    /**
     * Устанавливает параметры из пар в массиве
     * @param string[] $pairs Пары \[ключ]\[значение]
     */
    public function setParamsPairs($pairs): void
    {
        foreach ($pairs as $name) {
            $value = next($pairs) === false ? null : current($pairs);
            $this->params[$name] = $value;
        }
    }

    /**
     * Возвращает значение входного параметра
     * @param string $name Имя параметра
     * @param mixed $default Значение по-умолчанию
     * @return mixed
     */
    public function getParam(string $name, $default = null)
    {
        $result = array_key_exists($name, $this->params)
            ? $this->params[$name]
            : $this->request->get($name);

        return $result ?? $default;
    }

    /**
     * Проверяет существования параметра по имени
     * @param string $name Имя параметра
     * @return bool
     */
    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->params) || isset($this->request[$name]);
    }

    /**
     * Геттер для свойства `request`
     * @return HttpRequest
     */
    public function getRequest(): HttpRequest
    {
        return $this->request;
    }

    /**
     * Геттер для свойства `view`
     * @return ViewInterface|null
     */
    public function getView(): ?ViewInterface
    {
        return $this->view;
    }

    /**
     * Сеттер для свойства `view`
     * @param ViewInterface|null $view
     * @return static
     */
    public function setView(?ViewInterface $view): ControllerInterface
    {
        $this->view = $view;
        return $this;
    }
}
