# Утилитарные классы

## Common

### `loadModules()`

Метод загружает модули Битрикс или выбрасывает исключение, если какой-то модуль не может быть загружен.

```php
\spaceonfire\BitrixTools\Common::loadModules(['iblock', 'vendor.module']);
```

### `getAppException()`

Метод конвертирует сообщение об ошибке из глобального `$APPLICATION` в исключение.

```php
if (true/* error occurred in old api */) {
    throw \spaceonfire\BitrixTools\Common::getAppException();
}
```

## ORMTools

### `wrapTransaction()`

Метод позволяет выполнить переданный коллбэк внутри транзакции БД. Транзакция будет откачена, если при выполнении коллбэка
будет выброшено исключение. Возвращаемый коллбэком результат возвращается методом `wrapTransaction`, а также может послужить
индикатором успеха выполнения. Если возвращается `false` или результат не успешного выполнения D7 (`Bitrix\Main\Result`),
то транзакция так же будет откачена.

```php
use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;
use spaceonfire\BitrixTools\ORMTools;

// $queryResult - результат выполнения запроса внутри коллбэка
$queryResult = ORMTools::wrapTransaction(function (Connection $connection) {
    return $connection->query('UPDATE b_vendor_module_table SET foo="bar" WHERE foo="baz"');
}, Application::getConnection())
```

### `extractPrimary()`

Метод извлекает первичный ключ для переданной сущности Битрикс ORM из переданного массива данных.

```php
use spaceonfire\BitrixTools\ORMTools;

class MyEntityTable extends Bitrix\Main\ORM\Data\DataManager
{
}

$primary = ORMTools::extractPrimary(MyEntityTable::getEntity(), ['id' => 42, 'foo' => 'bar']);
// $primary === ['id' => 42]
```

### `collectValuesFromEntityObject()`

Позволяет рекурсивно извлечь данные из `Bitrix\Main\ORM\Objectify\EntityObject` в обычный `stdClass`

```php
use spaceonfire\BitrixTools\ORMTools;

class MyEntityTable extends Bitrix\Main\ORM\Data\DataManager
{
}

$entityObject = MyEntityTable::getByPrimary(42)->fetchObject();
$stdClassEntity = ORMTools::collectValuesFromEntityObject($entityObject);
```

## Cache

### `cacheResult()`

Позволяет закэшировать результат выполнения переданного коллбэка. На вход принимает также настройки для кэширования в
виде массива со следующими ключами:

-   `CACHE_ID` - ID кэша (строка, обязательный параметр);
-   `CACHE_PATH` - Относительный путь для сохранения кэша (строка, обязательный параметр). К нему автоматически будет
    добавлен ID сайта и `CACHE_TAG`, если указан;
-   `CACHE_TAG` - Включает использование тегированного кэша с переданными тэгами (строка или массив строк);
-   `CACHE_TIME` - Время жизни кэша в секундах, по-умолчанию одна неделя.

Третьим аргументом можно передать массив аргументов для коллбэка, тогда они так же будут добавлены к `CACHE_ID`.

```php
use spaceonfire\BitrixTools\Cache;

$cacheOptions = [
    'CACHE_ID' => 'my-cache-id',
    'CACHE_PATH' => '/my/cache/path',
    'CACHE_TIME' => 3600,
];

$result = Cache::cacheResult($cacheOptions, static function ($arg1, $arg2) {
    // $arg1 === 'foo' && $arg2 === 'bar'

    // Do something...
    return [
        $arg1 => $arg2,
    ];
}, ['foo', 'bar'])

// $result === ['foo' => 'bar']
```

### `clearCache()`

Данный метод удаляет кэш по переданным настройкам кэширования (формат настроек описан выше).

```php
use spaceonfire\BitrixTools\Cache;

Cache::clearCache([
   'CACHE_ID' => 'my-cache-id',
   'CACHE_PATH' => '/my/cache/path',
   'CACHE_TIME' => 3600,
]);
```

## Nav

### `normalizeMenuNav()`

Преобразует стандартный плоский массив навигационного меню, сгенерированный компонентом `bitrix:menu`,
в многоуровневый вложенный массив.

```php
<?php
// result_modifier.php своего шаблона компонента bitrix:menu

(defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) || die();

/**
 * @var $arResult array
 * @var $arParams array
 * @var $component CBitrixMenuComponent
 * @var $this CBitrixComponentTemplate
 * @global $APPLICATION CMain
 * @global $USER CUser
 */

use spaceonfire\BitrixTools\Nav;

$arResult = Nav::normalizeMenuNav($arResult);
```

```php
<?php
// template.php сам шаблон

(defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) || die();

/**
 * @var $arResult array
 * @var $arParams array
 * @var $component CBitrixMenuComponent
 * @var $this CBitrixComponentTemplate
 * @global $APPLICATION CMain
 * @global $USER CUser
 */

foreach ($arResult as $i => $item) {
    if ($item['IS_PARENT']) {
        foreach ($item['CHILDREN'] as $childItem) {
            //
        }
    }
}
```

### `isUserHasAccessToFile()`

Проверяет есть ли у пользователя доступ к переданному файлу. Можно использовать при описании навигационного меню
для фильтрации элементов, к которым у пользователя нет доступа.

```php
// Файл .top.menu_ext.php

// ...

$aMenuLinksExt[] = [
    'Menu Item',
    SITE_DIR . 'private/',
    [],
    [],
    'spaceonfire\BitrixTools\Nav::isUserHasAccessToFile(\'' . SITE_DIR . 'private/' . '\')'
];

// ...
```

## IblockTools

### `getSectionsTree()`

Возвращает список разделов инфоблока, выравненные по вложенности точками.

```php
use spaceonfire\BitrixTools\IblockTools;
use Webmozart\Assert\Assert;

$sectionsTree = IblockTools::getSectionsTree(IblockTools::getIblockIdByCode('my-iblock'));

Assert::eq($sectionsTree, [
    'Root',
    ' . Level 1',
    ' . . Level 2',
    ' . . . Level 3',
    ' . Level 1.a',
    ' . Level 1.b',
    ' . . Level 2.a',
    // ...
]);
```

### `getProperties()`

Возвращает список свойств инфоблока, проиндексированных по символьному коду.

### `getPropertyCodeById()`

Возвращает символьный код свойства инфоблока по его ID.

### `getPropertyIdByCode()`

Возвращает ID свойства инфоблока по его коду.

### `getEnums()`

Возвращает значения всех свойств инфоблока типа "список".

### `getEnumValueById()`

Возвращает значение enum свойства инфоблока по его ID.

### `getEnumValueByXmlId()`

Возвращает значение enum свойства инфоблока по его XML_ID.

### `getEnumIdByXmlId()`

Возвращает ID значения enum свойства инфоблока по его XML_ID.

### `getEnumXmlIdById()`

Возвращает XML_ID значения enum свойства по его ID.

### `getElementMeta()`

Возвращает SEO мета-данные для элемента инфоблока.

```php
use spaceonfire\BitrixTools\IblockTools;

/**
 * @global CMain $APPLICATION
 */

$elementMeta = IblockTools::getElementMeta(IblockTools::getIblockIdByCode('my-iblock'), 42);

$APPLICATION->SetPageProperty('title', $elementMeta['ELEMENT_META_TITLE']);
$APPLICATION->SetPageProperty('keywords', $elementMeta['ELEMENT_META_KEYWORDS']);
$APPLICATION->SetPageProperty('description', $elementMeta['ELEMENT_META_DESCRIPTION']);
```

### `getSectionMeta()`

Возвращает SEO мета-данные для раздела инфоблока.

```php
use spaceonfire\BitrixTools\IblockTools;

/**
 * @global CMain $APPLICATION
 */

$sectionMeta = IblockTools::getSectionMeta(IblockTools::getIblockIdByCode('my-iblock'), 24);

$APPLICATION->SetPageProperty('title', $sectionMeta['SECTION_META_TITLE']);
$APPLICATION->SetPageProperty('keywords', $sectionMeta['SECTION_META_KEYWORDS']);
$APPLICATION->SetPageProperty('description', $sectionMeta['SECTION_META_DESCRIPTION']);
```

## ArrayTools

В качестве базового класса используется класс `spaceonfire\Collection\ArrayHelper` из библиотеки
[`spaceonfire/collection`][link-spaceonfire-collection]. Желательно использовать его напрямую.

### `removeTildaKeys()`

Удаляет из ассоциативного массива ключи, начинающиеся с тильды `~`.

```php
use spaceonfire\BitrixTools\ArrayTools;

$noTildaKeys = ArrayTools::removeTildaKeys(['NAME' => 'Foo', '~NAME' => 'Foo']);
// $noTildaKeys === ['NAME' => 'Foo']
```

### `flatten()`

Конвертирует вложенный ассоциативный массив в одноуровневый, ключи которых склеиваются переданным разделителем.
По-умолчанию, в качестве разделителя используется точка.

```php
use spaceonfire\BitrixTools\ArrayTools;
use Webmozart\Assert\Assert;

$multiDimension = [
    'id' => 42,
    'foo' => [
        'bar' => 'baz',
    ],
];

$flatten = ArrayTools::flatten($multiDimension);

Assert::eq($flatten, [
    'id' => 42,
    'foo.bar' => 'baz',
]);
```

### `unflatten()`

Делает обратную операцию, конвертирует одноуровневый ассоциативный массив во вложенный, разбивая ключи по переданному разделителю.
По-умолчанию, в качестве разделителя используется точка.

```php
use spaceonfire\BitrixTools\ArrayTools;
use Webmozart\Assert\Assert;

$flatten = [
   'id' => 42,
   'foo.bar' => 'baz',
];

$multiDimension = ArrayTools::unflatten($flatten);

Assert::eq($multiDimension, [
    'id' => 42,
    'foo' => [
        'bar' => 'baz',
    ],
]);
```

### `isArrayAssoc()`

Проверяет, является ли массив ассоциативным. Массив не считается ассоциативным,
если его ключами является последовательность чисел, которая начинается с 0.

```php
use spaceonfire\BitrixTools\ArrayTools;
use Webmozart\Assert\Assert;

Assert::true(ArrayTools::isArrayAssoc(['foo' => 'bar']));
Assert::true(ArrayTools::isArrayAssoc([42 => ['id' => 42, 'foo' => 'bar']]));
Assert::true(ArrayTools::isArrayAssoc([3 => 'foo', 4 => 'bar', 6 => 'baz']));
Assert::false(ArrayTools::isArrayAssoc([0 => 'foo', 1 => 'bar', 2 => 'baz']));
```

### `merge()`

Рекурсивный мерж нескольких массивов

```php
use spaceonfire\BitrixTools\ArrayTools;
use Webmozart\Assert\Assert;

$a = [
    'version' => '1.0',
    'options' => [
        'option_a' => false,
        'option_b' => false,
    ],
    'features' => [
        'mvc',
    ],
];
$b = [
    'version' => '1.1',
    'options' => [
        'option_b' => true,
    ],
    'features' => [
        'orm',
    ],
];
$c = [
    'version' => '2.0',
    'options' => [
        'option_a' => true,
    ],
    'features' => [
        'debug',
    ],
    'foo',
];

$result = ArrayTools::merge($a, $b, $c);

Assert::eq($result, [
    'version' => '2.0',
    'options' => [
        'option_a' => true,
        'option_b' => true,
    ],
    'features' => [
        'mvc',
        'orm',
        'debug',
    ],
    'foo',
]);
```

## HttpStatusTools

Для работы с ошибками HTTP используется библиотека [`narrowspark/http-status`][link-narrowspark-http-status]
(например, в [компонентах][link-docs-components]). Данный утилитарный класс дополняет `Narrowspark\HttpStatus\HttpStatus`
методом `catchError()`.

### `catchError()`

Устанавливает статус ответа и константу ошибки `ERROR_<status_code>`. Если передано исключение, которое не реализует
интерфейс `Narrowspark\HttpStatus\Contract\Exception\HttpException`, будет установлена ошибка 500 Internal Server Error.

```php
use Narrowspark\HttpStatus\Exception\NotFoundException;
use spaceonfire\BitrixTools\HttpStatusTools;

HttpStatusTools::catchError(new NotFoundException('error')); // 404
HttpStatusTools::catchError(new RuntimeException('error')); // 500
```

[link-narrowspark-http-status]: https://github.com/narrowspark/http-status
[link-spaceonfire-collection]: https://github.com/spaceonfire/collection
[link-docs-components]: ./04-components.md
