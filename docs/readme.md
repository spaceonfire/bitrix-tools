# spaceonfire\BitrixTools

## Table of Contents

-   [ArrayTools](#arraytools)
    -   [flatten](#flatten)
    -   [unflatten](#unflatten)
    -   [removeTildaKeys](#removetildakeys)
    -   [isArrayAssoc](#isarrayassoc)
    -   [merge](#merge)
-   [Basket](#basket)
    -   [\_\_construct](#__construct)
    -   [factory](#factory)
    -   [doAction](#doaction)
    -   [setParamsPairs](#setparamspairs)
    -   [removeAction](#removeaction)
    -   [updateCountAction](#updatecountaction)
-   [Cache](#cache)
    -   [cacheResult](#cacheresult)
-   [Common](#common)
    -   [loadModules](#loadmodules)
    -   [addBodyClass](#addbodyclass)
-   [EventHandler](#eventhandler)
    -   [boot](#boot)
    -   [OnAfterIBlockPropertyAdd](#onafteriblockpropertyadd)
    -   [OnAfterIBlockPropertyUpdate](#onafteriblockpropertyupdate)
    -   [OnBeforeIBlockPropertyDelete](#onbeforeiblockpropertydelete)
-   [Form](#form)
    -   [\_\_construct](#__construct-1)
    -   [factory](#factory-1)
    -   [doAction](#doaction-1)
    -   [setParamsPairs](#setparamspairs-1)
    -   [feedbackAction](#feedbackaction)
    -   [callbackAction](#callbackaction)
    -   [resultAction](#resultaction)
-   [Html](#html)
    -   [\_\_construct](#__construct-2)
    -   [sendHeaders](#sendheaders)
    -   [render](#render)
    -   [setData](#setdata)
    -   [setBaseDir](#setbasedir)
    -   [getPath](#getpath)
-   [IblockPropMultiple](#iblockpropmultiple)
    -   [getIblockId](#getiblockid)
    -   [getTableName](#gettablename)
    -   [getMap](#getmap)
-   [IblockSection](#iblocksection)
    -   [getIblockId](#getiblockid-1)
    -   [getList](#getlist)
    -   [getMap](#getmap-1)
-   [IblockSectionPropSimple](#iblocksectionpropsimple)
    -   [getIblockId](#getiblockid-2)
    -   [getTableName](#gettablename-1)
    -   [getFilePath](#getfilepath)
    -   [getMap](#getmap-2)
-   [Json](#json)
    -   [\_\_construct](#__construct-3)
    -   [sendHeaders](#sendheaders-1)
    -   [render](#render-1)
    -   [setData](#setdata-1)
    -   [setBaseDir](#setbasedir-1)
    -   [getPath](#getpath-1)
-   [Nav](#nav)
    -   [normalizeMenuNav](#normalizemenunav)
    -   [isUserHasAccessToFile](#isuserhasaccesstofile)
-   [Php](#php)
    -   [\_\_construct](#__construct-4)
    -   [sendHeaders](#sendheaders-2)
    -   [render](#render-2)
    -   [setData](#setdata-2)
    -   [setBaseDir](#setbasedir-2)
    -   [getPath](#getpath-2)
    -   [escape](#escape)
-   [Prototype](#prototype)
    -   [\_\_construct](#__construct-5)
    -   [sendHeaders](#sendheaders-3)
    -   [render](#render-3)
    -   [setData](#setdata-3)
    -   [setBaseDir](#setbasedir-3)
    -   [getPath](#getpath-3)
-   [Prototype](#prototype-1)
    -   [\_\_construct](#__construct-6)
    -   [factory](#factory-2)
    -   [doAction](#doaction-2)
    -   [setParamsPairs](#setparamspairs-2)
-   [SectionElementTable](#sectionelementtable)
-   [Xml](#xml)
    -   [\_\_construct](#__construct-7)
    -   [sendHeaders](#sendheaders-4)
    -   [render](#render-4)
    -   [setData](#setdata-4)
    -   [setBaseDir](#setbasedir-4)
    -   [getPath](#getpath-4)

## ArrayTools

-   Full name: \spaceonfire\BitrixTools\ArrayTools

### flatten

Конвертирует вложенный ассоциативный массив в одноуровневый

```php
ArrayTools::flatten( array $array, string $separator = &#039;.&#039;, string $prefix = &#039;&#039; ): array
```

-   This method is **static**.

**Parameters:**

| Parameter    | Type       | Description                                                                              |
| ------------ | ---------- | ---------------------------------------------------------------------------------------- |
| `$array`     | **array**  | Исходный вложенный массив                                                                |
| `$separator` | **string** | Строка для склеивания ключей, по-умолчанию '.'                                           |
| `$prefix`    | **string** | Префикс для ключей, в основном нужен для рекурсивных вызовов, по-умолчанию пустая строка |

**Return Value:**

Одноуровневый массив

---

### unflatten

Конвертирует одноуровневый ассоциативный массив во вложенный, разбивая ключи по \$separator

```php
ArrayTools::unflatten( array $array, string $separator = &#039;.&#039; ): array
```

-   This method is **static**.

**Parameters:**

| Parameter    | Type       | Description                                     |
| ------------ | ---------- | ----------------------------------------------- |
| `$array`     | **array**  | Исходный одноуровневый ассоциативный массив     |
| `$separator` | **string** | Подстрока для разбивки ключей, по-умолчанию '.' |

**Return Value:**

Многоуровневый массив

---

### removeTildaKeys

Удаляет из ассоциативного массива ключи, начинающиеся с тильды (~)

```php
ArrayTools::removeTildaKeys( array $data ): array
```

-   This method is **static**.

**Parameters:**

| Parameter | Type      | Description                             |
| --------- | --------- | --------------------------------------- |
| `$data`   | **array** | Исходный ассоциативный массив с данными |

**Return Value:**

Массив с удаленными ключами

---

### isArrayAssoc

Проверяет, является ли массив ассоциативный (есть хотябы один строковый ключ)

```php
ArrayTools::isArrayAssoc( mixed $var ): boolean
```

-   This method is **static**.

**Parameters:**

| Parameter | Type      | Description             |
| --------- | --------- | ----------------------- |
| `$var`    | **mixed** | Переменная для проверки |

---

### merge

Рекурсивный мерж нескольких массивов

```php
ArrayTools::merge( array $arrays ): array
```

-   This method is **static**.

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$arrays` | **array** |             |

---

## Basket

Контроллер корзины

-   Full name: \spaceonfire\BitrixTools\Mvc\Controller\Basket
-   Parent class: \spaceonfire\BitrixTools\Mvc\Controller\Prototype

### \_\_construct

Создает новый контроллер

```php
Basket::__construct(  ): void
```

---

### factory

"Фабрика" контроллеров

```php
Basket::factory( string $name, string $namespace = __NAMESPACE__ ): \spaceonfire\BitrixTools\Mvc\Controller\Prototype
```

-   This method is **static**.

**Parameters:**

| Parameter    | Type       | Description      |
| ------------ | ---------- | ---------------- |
| `$name`      | **string** | Имя сущности     |
| `$namespace` | **string** | Неймспейс класса |

---

### doAction

Выполняет экшн контроллера

```php
Basket::doAction( string $name ): void
```

**Parameters:**

| Parameter | Type       | Description |
| --------- | ---------- | ----------- |
| `$name`   | **string** | Имя экшена  |

---

### setParamsPairs

Устанавливает параметры из пар в массиве

```php
Basket::setParamsPairs( array $pairs ): void
```

**Parameters:**

| Parameter | Type      | Description           |
| --------- | --------- | --------------------- |
| `$pairs`  | **array** | Пары [ключ][значение] |

---

### removeAction

Удаляет товар из корзины

```php
Basket::removeAction(  ): void
```

---

### updateCountAction

Изменение количества покупаемого товара

```php
Basket::updateCountAction(  )
```

---

## Cache

-   Full name: \spaceonfire\BitrixTools\Cache

### cacheResult

Кэширует результаты выполнения функции \$callback

```php
Cache::cacheResult( array $options, callable $callback, array $args = array() ): mixed
```

-   This method is **static**.

**Parameters:**

| Parameter  | Type      | Description                          |
| ---------- | --------- | ------------------------------------ |
| `$options` | **array** | Массив с параметрами для кэширования |

     $options = [
         'CACHE_ID' => (string) ID кэша (обязательный параметр)
         'CACHE_PATH' => (string) Относительный путь для сохранения кэша (обязательный параметр). Будет автоматически добавлен ID сайта и CACHE_TAG, если указан
         'CACHE_TAG' => (string) Включает использование тегированного кэша с переданным тэгом
         'CACHE_TIME' => (int) Время жизни кэша (TTL) в секундах, по-умолчанию 36000000
     ] |

| `$callback` | **callable** | Функция, выполнение которой необходимо кэшировать |
| `$args` | **array** | Массив аргументов для функции \$callback |

**Return Value:**

Данные возвращаемые функцией \$callback из кэша

---

## Common

-   Full name: \spaceonfire\BitrixTools\Common

### loadModules

Загружает модули 1С-Битрикс

```php
Common::loadModules( array $modules )
```

-   This method is **static**.

**Parameters:**

| Parameter  | Type      | Description                                  |
| ---------- | --------- | -------------------------------------------- |
| `$modules` | **array** | Массив модулей, которые необходимо загрузить |

---

### addBodyClass

Добавляет классы к body

```php
Common::addBodyClass( array $classes, string $propertyId = &#039;BodyClass&#039; )
```

-   This method is **static**.

**Parameters:**

| Parameter     | Type       | Description                                                                          |
| ------------- | ---------- | ------------------------------------------------------------------------------------ |
| `$classes`    | **array**  | Массив классов                                                                       |
| `$propertyId` | **string** | ID своего свойства для хранения классов body, по-умолчанию, 'BodyClass' для Bitrix24 |

---

## EventHandler

-   Full name: \spaceonfire\BitrixTools\ORM\EventHandler

### boot

Регистрирует сброс кэша инфоблока при действиях над свойствами инфоблока

```php
EventHandler::boot(  )
```

-   This method is **static**.

---

### OnAfterIBlockPropertyAdd

```php
EventHandler::OnAfterIBlockPropertyAdd(  $arFields )
```

-   This method is **static**.

**Parameters:**

| Parameter   | Type     | Description |
| ----------- | -------- | ----------- |
| `$arFields` | \*\*\*\* |             |

---

### OnAfterIBlockPropertyUpdate

```php
EventHandler::OnAfterIBlockPropertyUpdate(  $arFields )
```

-   This method is **static**.

**Parameters:**

| Parameter   | Type     | Description |
| ----------- | -------- | ----------- |
| `$arFields` | \*\*\*\* |             |

---

### OnBeforeIBlockPropertyDelete

```php
EventHandler::OnBeforeIBlockPropertyDelete(  $ID )
```

-   This method is **static**.

**Parameters:**

| Parameter | Type     | Description |
| --------- | -------- | ----------- |
| `$ID`     | \*\*\*\* |             |

---

## Form

Контроллер веб-форм

-   Full name: \spaceonfire\BitrixTools\Mvc\Controller\Form
-   Parent class: \spaceonfire\BitrixTools\Mvc\Controller\Prototype

### \_\_construct

Создает новый контроллер

```php
Form::__construct(  ): void
```

---

### factory

"Фабрика" контроллеров

```php
Form::factory( string $name, string $namespace = __NAMESPACE__ ): \spaceonfire\BitrixTools\Mvc\Controller\Prototype
```

-   This method is **static**.

**Parameters:**

| Parameter    | Type       | Description      |
| ------------ | ---------- | ---------------- |
| `$name`      | **string** | Имя сущности     |
| `$namespace` | **string** | Неймспейс класса |

---

### doAction

Выполняет экшн контроллера

```php
Form::doAction( string $name ): void
```

**Parameters:**

| Parameter | Type       | Description |
| --------- | ---------- | ----------- |
| `$name`   | **string** | Имя экшена  |

---

### setParamsPairs

Устанавливает параметры из пар в массиве

```php
Form::setParamsPairs( array $pairs ): void
```

**Parameters:**

| Parameter | Type      | Description           |
| --------- | --------- | --------------------- |
| `$pairs`  | **array** | Пары [ключ][значение] |

---

### feedbackAction

Выводит форму обратной связи

```php
Form::feedbackAction(  ): string
```

---

### callbackAction

Выводит форму обратного звонка

```php
Form::callbackAction(  ): string
```

---

### resultAction

Выводит результат заполнения формы

```php
Form::resultAction(  ): array
```

---

## HighLoadBlock

Class HighLoadBlock

-   Full name: \spaceonfire\BitrixTools\ORM\HighLoadBlock

### add

```php
HighLoadBlock::add( array $data ): \Bitrix\Main\ORM\Data\AddResult
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$data`   | **array** |             |

---

### checkFields

```php
HighLoadBlock::checkFields( \ORM\Data\Result $result, mixed $primary, array $data ): void
```

**Parameters:**

| Parameter  | Type                 | Description |
| ---------- | -------------------- | ----------- |
| `$result`  | **\ORM\Data\Result** |             |
| `$primary` | **mixed**            |             |
| `$data`    | **array**            |             |

---

### delete

```php
HighLoadBlock::delete( mixed $primary ): \Bitrix\Main\ORM\Data\DeleteResult
```

**Parameters:**

| Parameter  | Type      | Description |
| ---------- | --------- | ----------- |
| `$primary` | **mixed** |             |

---

### getById

```php
HighLoadBlock::getById( mixed $id ): \Bitrix\Main\ORM\Query\Result
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$id`     | **mixed** |             |

---

### getByPrimary

```php
HighLoadBlock::getByPrimary( mixed $id ): \Bitrix\Main\ORM\Query\Result
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$id`     | **mixed** |             |

---

### getCount

```php
HighLoadBlock::getCount( \= $filter ): integer
```

, array \$cache = array())

**Parameters:**

| Parameter | Type   | Description |
| --------- | ------ | ----------- |
| `$filter` | **\=** |             |

---

### getList

```php
HighLoadBlock::getList( array $parameters = array( ): \Bitrix\Main\ORM\Query\Result
```

)

**Parameters:**

| Parameter     | Type      | Description |
| ------------- | --------- | ----------- |
| `$parameters` | **array** |             |

---

### getMap

```php
HighLoadBlock::getMap(  ): array
```

---

### getTableName

```php
HighLoadBlock::getTableName(  ): string
```

---

### getUfId

```php
HighLoadBlock::getUfId(  ): string
```

---

### query

```php
HighLoadBlock::query(  ): \Bitrix\Main\ORM\Query\Query
```

---

### update

```php
HighLoadBlock::update( mixed $primary, array $data ): \Bitrix\Main\ORM\Data\UpdateResult
```

**Parameters:**

| Parameter  | Type      | Description |
| ---------- | --------- | ----------- |
| `$primary` | **mixed** |             |
| `$data`    | **array** |             |

---

### getHLId

```php
HighLoadBlock::getHLId(  ): integer|string
```

-   This method is **static**.

---

### getEntity

Возвращает скомпилированную сущность HighLoad блока

```php
HighLoadBlock::getEntity(  ): \Bitrix\Main\ORM\Entity
```

-   This method is **static**.

---

### cleanEntityCache

Очистка кэша по сущности

```php
HighLoadBlock::cleanEntityCache(  )
```

-   This method is **static**.

---

### mergeOrmParameters

Расширяет параметры запроса

```php
HighLoadBlock::mergeOrmParameters( array $parameters = array() ): array
```

В то время, как наследники данного класса являются просто прокси к скомпилированной сущности
HighLoad блока, мы не можем просто переопределить метод `getMap()`. Но мы можем расширять поля
`runtime` и другие параметры запроса переопределив данный метод.

-   This method is **static**.

**Parameters:**

| Parameter     | Type      | Description |
| ------------- | --------- | ----------- |
| `$parameters` | **array** |             |

---

### \_\_callStatic

Проксируем вызовы методов в data class скомпилированной сущности

```php
HighLoadBlock::__callStatic( string $name, array $arguments ): mixed
```

-   This method is **static**.

**Parameters:**

| Parameter    | Type       | Description |
| ------------ | ---------- | ----------- |
| `$name`      | **string** | method name |
| `$arguments` | **array**  | arguments   |

---

## Html

HTML MVC view

-   Full name: \spaceonfire\BitrixTools\Mvc\View\Html
-   Parent class: \spaceonfire\BitrixTools\Mvc\View\Prototype

### \_\_construct

Создает новый MVC HTML view

```php
Html::__construct( string $data = &#039;&#039; ): void
```

**Parameters:**

| Parameter | Type       | Description |
| --------- | ---------- | ----------- |
| `$data`   | **string** | HTML текст  |

---

### sendHeaders

Отсылает http-заголовки для view

```php
Html::sendHeaders(  ): void
```

---

### render

Формирует view

```php
Html::render(  ): string
```

---

### setData

Устанавливает данные

```php
Html::setData( mixed $data ): void
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$data`   | **mixed** | Данные      |

---

### setBaseDir

Устанавливает базовый каталог

```php
Html::setBaseDir( string $dir ): void
```

**Parameters:**

| Parameter | Type       | Description     |
| --------- | ---------- | --------------- |
| `$dir`    | **string** | Базовый каталог |

---

### getPath

Возвращает путь до файла шаблона

```php
Html::getPath(  ): string
```

---

## IblockPropMultiple

-   Full name: \spaceonfire\BitrixTools\ORM\IblockPropMultiple
-   Parent class:

### getIblockId

Возвращает ID инфоблока. Необходимо переопределять метод.

```php
IblockPropMultiple::getIblockId(  ): integer
```

-   This method is **static**.

---

### getTableName

Возвращает название таблицы для сущности в БД

```php
IblockPropMultiple::getTableName(  ): string
```

-   This method is **static**.

---

### getMap

Возврщает схему полей сущности

```php
IblockPropMultiple::getMap(  ): array
```

-   This method is **static**.

---

## IblockPropSimple

-   Full name: \spaceonfire\BitrixTools\ORM\IblockPropSimple
-   Parent class:

### getIblockId

Возвращает ID инфоблока. Необходимо переопределять метод.

```php
IblockPropSimple::getIblockId(  ): integer
```

-   This method is **static**.

---

### getTableName

Возвращает название таблицы для сущности в БД

```php
IblockPropSimple::getTableName(  ): string
```

-   This method is **static**.

---

### getMap

Возврщает схему полей сущности

```php
IblockPropSimple::getMap(  ): array
```

-   This method is **static**.

---

## IblockSection

-   Full name: \spaceonfire\BitrixTools\ORM\IblockSection
-   Parent class:

### getIblockId

Возвращает ID инфоблока. Необходимо переопределять метод.

```php
IblockSection::getIblockId(  ): integer
```

-   This method is **static**.

---

### getList

```php
IblockSection::getList( array $parameters = array() )
```

-   This method is **static**.

**Parameters:**

| Parameter     | Type      | Description |
| ------------- | --------- | ----------- |
| `$parameters` | **array** |             |

---

### getMap

Возврщает схему полей сущности

```php
IblockSection::getMap(  ): array
```

-   This method is **static**.

---

## IblockSectionPropSimple

Class IblockSectionPropSimple

If you are using access user fields using @see \Bitrix\Main\Entity\DataManager::getUfId ,
you may encounter problem when need to do a join on the value of the property.
Bitrix orm generates wrong alias for the join table.
To resolve this problem, use this class.

-   Full name: \spaceonfire\BitrixTools\ORM\IblockSectionPropSimple
-   Parent class:

### getIblockId

```php
IblockSectionPropSimple::getIblockId(  ): integer
```

-   This method is **static**.

---

### getTableName

```php
IblockSectionPropSimple::getTableName(  )
```

-   This method is **static**.

---

### getFilePath

```php
IblockSectionPropSimple::getFilePath(  )
```

-   This method is **static**.

---

### getMap

```php
IblockSectionPropSimple::getMap(  )
```

-   This method is **static**.

---

## Json

JSON MVC view

-   Full name: \spaceonfire\BitrixTools\Mvc\View\Json
-   Parent class: \spaceonfire\BitrixTools\Mvc\View\Prototype

### \_\_construct

Создает новый MVC JSON view

```php
Json::__construct( mixed $data = array() ): void
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$data`   | **mixed** | Данные view |

---

### sendHeaders

Отсылает http-заголовки для view

```php
Json::sendHeaders(  ): void
```

---

### render

Формирует view

```php
Json::render(  ): string
```

---

### setData

Устанавливает данные

```php
Json::setData( mixed $data ): void
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$data`   | **mixed** | Данные      |

---

### setBaseDir

Устанавливает базовый каталог

```php
Json::setBaseDir( string $dir ): void
```

**Parameters:**

| Parameter | Type       | Description     |
| --------- | ---------- | --------------- |
| `$dir`    | **string** | Базовый каталог |

---

### getPath

Возвращает путь до файла шаблона

```php
Json::getPath(  ): string
```

---

## Nav

-   Full name: \spaceonfire\BitrixTools\Nav

### normalizeMenuNav

Преобразует стандартный плоский массив навигационного меню, сгенерированный компонентом bitrix:menu,
в многоуровневый вложенный массив

```php
Nav::normalizeMenuNav( array $nav ): array
```

-   This method is **static**.

**Parameters:**

| Parameter | Type      | Description                                          |
| --------- | --------- | ---------------------------------------------------- |
| `$nav`    | **array** | Массив навигационного меню из компонента bitrix:menu |

**Return Value:**

Преобразованный многоуровневый массив

---

### isUserHasAccessToFile

Проверяет есть ли у пользователя доступ к \$path

```php
Nav::isUserHasAccessToFile( string $path ): boolean
```

-   This method is **static**.

**Parameters:**

| Parameter | Type       | Description                               |
| --------- | ---------- | ----------------------------------------- |
| `$path`   | **string** | Путь к файлу или папке относительно корня |

---

## Php

PHP MVC view

-   Full name: \spaceonfire\BitrixTools\Mvc\View\Php
-   Parent class: \spaceonfire\BitrixTools\Mvc\View\Prototype

### \_\_construct

Создает новый MVC view

```php
Php::__construct( string $name = &#039;&#039;, mixed $data = array(), string $baseDir = &#039;&#039; )
```

**Parameters:**

| Parameter  | Type       | Description           |
| ---------- | ---------- | --------------------- |
| `$name`    | **string** | Название шаблона view |
| `$data`    | **mixed**  | Данные view           |
| `$baseDir` | **string** |                       |

---

### sendHeaders

Отсылает http-заголовки для view

```php
Php::sendHeaders(  ): void
```

---

### render

Формирует view

```php
Php::render(  ): string
```

---

### setData

Устанавливает данные

```php
Php::setData( mixed $data ): void
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$data`   | **mixed** | Данные      |

---

### setBaseDir

Устанавливает базовый каталог

```php
Php::setBaseDir( string $dir ): void
```

**Parameters:**

| Parameter | Type       | Description     |
| --------- | ---------- | --------------- |
| `$dir`    | **string** | Базовый каталог |

---

### getPath

Возвращает путь до файла шаблона

```php
Php::getPath(  ): string
```

---

### escape

Выводит HTML в безопасном виде

```php
Php::escape( string $data ): string
```

**Parameters:**

| Parameter | Type       | Description      |
| --------- | ---------- | ---------------- |
| `$data`   | **string** | Выводимые данные |

---

## Prototype

Абстрактный MVC view

-   Full name: \spaceonfire\BitrixTools\Mvc\View\Prototype

### \_\_construct

Создает новый MVC view

```php
Prototype::__construct( string $name = &#039;&#039;, mixed $data = array(), string $baseDir = &#039;&#039; )
```

**Parameters:**

| Parameter  | Type       | Description           |
| ---------- | ---------- | --------------------- |
| `$name`    | **string** | Название шаблона view |
| `$data`    | **mixed**  | Данные view           |
| `$baseDir` | **string** |                       |

---

### sendHeaders

Отсылает http-заголовки для view

```php
Prototype::sendHeaders(  ): void
```

---

### render

Формирует view

```php
Prototype::render(  ): string
```

---

### setData

Устанавливает данные

```php
Prototype::setData( mixed $data ): void
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$data`   | **mixed** | Данные      |

---

### setBaseDir

Устанавливает базовый каталог

```php
Prototype::setBaseDir( string $dir ): void
```

**Parameters:**

| Parameter | Type       | Description     |
| --------- | ---------- | --------------- |
| `$dir`    | **string** | Базовый каталог |

---

### getPath

Возвращает путь до файла шаблона

```php
Prototype::getPath(  ): string
```

---

## Prototype

Прототип MVC контроллера

-   Full name: \spaceonfire\BitrixTools\Mvc\Controller\Prototype

### \_\_construct

Создает новый контроллер

```php
Prototype::__construct(  ): void
```

---

### factory

"Фабрика" контроллеров

```php
Prototype::factory( string $name, string $namespace = __NAMESPACE__ ): \spaceonfire\BitrixTools\Mvc\Controller\Prototype
```

-   This method is **static**.

**Parameters:**

| Parameter    | Type       | Description      |
| ------------ | ---------- | ---------------- |
| `$name`      | **string** | Имя сущности     |
| `$namespace` | **string** | Неймспейс класса |

---

### doAction

Выполняет экшн контроллера

```php
Prototype::doAction( string $name ): void
```

**Parameters:**

| Parameter | Type       | Description |
| --------- | ---------- | ----------- |
| `$name`   | **string** | Имя экшена  |

---

### setParamsPairs

Устанавливает параметры из пар в массиве

```php
Prototype::setParamsPairs( array $pairs ): void
```

**Parameters:**

| Parameter | Type      | Description           |
| --------- | --------- | --------------------- |
| `$pairs`  | **array** | Пары [ключ][значение] |

---

## SectionElementTable

-   Full name: \spaceonfire\BitrixTools\ORM\SectionElementTable
-   Parent class:

## Xml

XML MVC view

-   Full name: \spaceonfire\BitrixTools\Mvc\View\Xml
-   Parent class: \spaceonfire\BitrixTools\Mvc\View\Prototype

### \_\_construct

Создает новый MVC XML view

```php
Xml::__construct( mixed $data = array() ): void
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$data`   | **mixed** | Данные view |

---

### sendHeaders

Отсылает http-заголовки для view

```php
Xml::sendHeaders(  ): void
```

---

### render

Формирует view

```php
Xml::render(  ): string
```

---

### setData

Устанавливает данные

```php
Xml::setData( mixed $data ): void
```

**Parameters:**

| Parameter | Type      | Description |
| --------- | --------- | ----------- |
| `$data`   | **mixed** | Данные      |

---

### setBaseDir

Устанавливает базовый каталог

```php
Xml::setBaseDir( string $dir ): void
```

**Parameters:**

| Parameter | Type       | Description     |
| --------- | ---------- | --------------- |
| `$dir`    | **string** | Базовый каталог |

---

### getPath

Возвращает путь до файла шаблона

```php
Xml::getPath(  ): string
```

---

---

> This document was automatically generated from source code comments on 2019-05-21 using [phpDocumentor](http://www.phpdoc.org/) and [spaceonfire/phpdoc-markdown-public](https://github.com/spaceonfire/phpdoc-markdown-public)
