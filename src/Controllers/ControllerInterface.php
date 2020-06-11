<?php

namespace spaceonfire\BitrixTools\Controllers;

use Bitrix\Main\HttpRequest;
use spaceonfire\BitrixTools\Views\ViewInterface;

interface ControllerInterface
{
    /**
     * Выполняет экшн контроллера
     * @param string $name Имя экшена
     */
    public function doAction($name): void;

    /**
     * Сеттер для свойства `params`
     * @param array $params
     */
    public function setParams(array $params): void;

    /**
     * Возвращает значение входного параметра
     * @param string $name Имя параметра
     * @param mixed $default Значение по-умолчанию
     * @return mixed
     */
    public function getParam(string $name, $default = null);

    /**
     * Проверяет существования параметра по имени
     * @param string $name Имя параметра
     * @return bool
     */
    public function hasParam(string $name): bool;

    /**
     * Геттер для свойства `request`
     * @return HttpRequest
     */
    public function getRequest(): HttpRequest;

    /**
     * Геттер для свойства `view`
     * @return ViewInterface|null
     */
    public function getView(): ?ViewInterface;

    /**
     * Сеттер для свойства `view`
     * @param ViewInterface|null $view
     * @return static
     */
    public function setView(?ViewInterface $view): ControllerInterface;
}
