<?php

namespace spaceonfire\BitrixTools\Views;

use RuntimeException;
use Throwable;

abstract class BaseView implements ViewInterface
{
    /**
     * @var string Каталог по умолчанию с файлами представлений
     */
    protected $baseDir = '';
    /**
     * @var string Имя представления
     */
    protected $name = '';
    /**
     * @var mixed Данные представления
     */
    protected $data;

    /**
     * Создает новый объект представления
     * @param string $name
     * @param mixed $data
     * @param string $baseDir
     */
    public function __construct(string $name = '', $data = [], string $baseDir = '')
    {
        $this->name = $name;
        $this->data = $data;

        if ($baseDir) {
            $this->setBaseDir($baseDir);
        }
    }

    /**
     * Отсылает http-заголовки для view
     */
    public function sendHeaders(): void
    {
    }

    /**
     * Рендеринг представления
     * @return string
     */
    public function render(): string
    {
        throw new RuntimeException('Abstract view can\'t be rendered.');
    }

    /**
     * Рендерит представление при попытке приведения к строке
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Throwable $throwable) {
            return '';
        }
    }

    /**
     * Геттер для свойства `data`
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Сеттер для свойства `data`
     * @param mixed $data Данные
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * Геттер для свойства `baseDir`
     * @return string
     */
    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    /**
     * Сеттер для свойства `baseDir`
     * @param string $baseDir
     */
    public function setBaseDir(string $baseDir): void
    {
        $this->baseDir = $baseDir;
    }

    /**
     * Геттер для свойства `name`
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает путь до файла шаблона
     * @return string
     */
    public function getPath(): string
    {
        return $this->baseDir . $this->name;
    }
}
