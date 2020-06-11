<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\Components\Property;

use CBitrixComponent;

trait ComponentPropertiesTrait
{
    /**
     * @var PropertyBag
     */
    protected $properties;

    protected function initPropertiesBag(): void
    {
        /** @var CBitrixComponent|self $this */
        $this->properties = new PropertyBag($this);
    }

    public function __get($name)
    {
        return $this->properties->__get($name);
    }

    public function __set($name, $value): void
    {
        $this->properties->__set($name, $value);
    }

    public function __isset($name): bool
    {
        return $this->properties->__isset($name);
    }

    public function __unset($name): void
    {
        $this->properties->__unset($name);
    }
}
