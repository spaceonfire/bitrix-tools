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
     * Универсальный порядок выполнения простого компонента
     */
    final protected function run(): void
    {
        $this->includeModules();
        $this->checkParams();
        $this->init();
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
     * @return $this|mixed возвращает текущий экземпляр компонента
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
