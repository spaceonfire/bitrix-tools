<?php

namespace spaceonfire\BitrixTools\Views;

interface ViewInterface
{
    /**
     * Отсылает http-заголовки для view
     */
    public function sendHeaders(): void;

    /**
     * Рендеринг представления
     * @return string
     */
    public function render(): string;

    /**
     * Возвращает путь до файла шаблона
     * @return string
     */
    public function getPath(): string;

    /**
     * Геттер для свойства `data`
     * @return mixed
     */
    public function getData();

    /**
     * Сеттер для свойства `data`
     * @param mixed $data Данные
     */
    public function setData($data): void;
}
