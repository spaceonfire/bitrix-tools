# Cache Map

При разработке сайта на Битриксе все время приходится завязываться на ID инфоблоков, "highload"-блоков, групп пользователей,
значениях справочников и пр. Т.к. эти ID могут различаться от окружения к окружению прописывать их в коде напрямую нельзя,
иначе это приведет к ошибкам в работе сайта. Решить эту проблему можно с помощью Cache Map.

Функционал cache map позволяет загружать массив данных из БД, кэшировать его и получать данные из него по индексному полю.
Например, загружает список инфоблоков, записывает данные в кэш, чтобы в следующий раз не делать запрос к БД,
и индексирует список по символьному коду. Теперь получить ID или все поля инфоблока можно из cache map по символьному коду.

Cache Map - это объект, реализующий интерфейс `spaceonfire\BitrixTools\CacheMap\CacheMap`. В пакете присутствует базовая
реализация данного интерфейса: `spaceonfire\BitrixTools\CacheMap\CacheMap\AbstractCacheMap`, которую можно использовать
для создания собственных cache map, если не нашли подходящий.

## Методы

-   `get($code)`

    Метод `get($code)` возвращает данные элемента (обычно - массив) по символьному коду или `null`, если такого элемента нет.

-   `getId($code)`

    Метод `getId($code)` возвращает ID элемента по символьному коду или `null`, если такого элемента нет.
    Если ID храниться в виде строки, но является целым числом, то перед возвратом приведет его к `int`.

-   `clearCache()`

    Очищает кэш

Так же cache map является итерируемым объектом. Это значит, что по содержащимся в нем элементам можно пройтись в цикле.

```php
/** @var $cacheMap \spaceonfire\BitrixTools\CacheMap\CacheMap */

foreach ($cacheMap as $item) {
    // do something
}
```

## Настройки Cache Map

Для описания настроек cache map используется класс `spaceonfire\BitrixTools\CacheMap\CacheMapOptions`, который принимает
в конструкторе уникальный идентификатор cache map, ключ идентификатора элемента, ключ индексного поля элемента,
ключ зависимости от регистра, настройки для кэширования (опционально).

```php
use spaceonfire\BitrixTools\CacheMap\CacheMapOptions;

$cacheMapOptions = new CacheMapOptions('my-cache-map-id', 'id', 'code', true, [
    'CACHE_ID' => 'my-cache-map-id',
    'CACHE_TAG' => 'some-cache-tag',
    'CACHE_PATH' => 'bitrix-tools/cache-map/my-cache-map-id',
    'CACHE_TIME' => 424242,
]);
```

## Доступные реализации

### ArrayCacheMap

Самый простой cache map, который даже не использует кэширование. На вход принимает набор элементов и объект настроек.

```php
use spaceonfire\BitrixTools\CacheMap\ArrayCacheMap;
use spaceonfire\BitrixTools\CacheMap\CacheMapOptions;
use Webmozart\Assert\Assert;

$cacheMap = new ArrayCacheMap([
    ['id' => 23, 'foo' => 'baz'],
    ['id' => 42, 'foo' => 'bar'],
], new CacheMapOptions('my-unique-id', 'id', 'foo'));

Assert::same($cacheMap->getId('bar'), 42);
Assert::same($cacheMap->getId('baz'), 23);
```

### QueryCacheMap

Данный cache map загружает данные из переданного объекта запроса к БД из ORM Битрикс.

```php
use spaceonfire\BitrixTools\CacheMap\QueryCacheMap;
use spaceonfire\BitrixTools\CacheMap\CacheMapOptions;

class MyDataTable extends Bitrix\Main\ORM\Data\DataManager
{
}

$cacheMap = new QueryCacheMap(
    MyDataTable::query()->setSelect(['*'])->whereNotNull('EXTERNAL_ID'),
    new CacheMapOptions('my-data-table', 'ID', 'EXTERNAL_ID')
);
```

### ClosureCacheMap

Данный cache map работает с данными, которые вернет переданная на вход функция. Эта функция, например, может выполнять
запрос к БД используя старое API Битрикс или производить какие-то вычисления, которые Вы заходите закэшировать пред тем,
как обращаться к ним.

```php
use spaceonfire\BitrixTools\CacheMap\ClosureCacheMap;
use spaceonfire\BitrixTools\CacheMap\CacheMapOptions;
use Webmozart\Assert\Assert;

$cacheMap = new ClosureCacheMap(static function () {
    return [
       ['id' => 23, 'foo' => 'baz'],
       ['id' => 42, 'foo' => 'bar'],
    ];
}, new CacheMapOptions('my-closure-cache-map-id', 'id', 'foo'));

Assert::same($cacheMap->getId('bar'), 42);
Assert::same($cacheMap->getId('baz'), 23);
```

### CustomCacheMap

Является декоратором над описанными выше cache map, принимает на вход любой из перечисленных источников данных: массив,
запрос Битрикс ORM или замыкание. Данный класс остается для обратной совместимости, вместо него рекомендуется
использовать отдельные классы, описанные выше.

## Статичные Cache Map

Описанные выше классы позволяют создавать cache map динамически, что не всегда удобно.
Для этих целей существует статичный cache map, реализующий интерфейс `spaceonfire\BitrixTools\CacheMap\StaticCacheMap`.
Он содержит те же методы, что и обычный cache map, только они статичные. Так же метод `getInstance()` позволяет получить
обычный cache map, например, если требуется пройтись в цикле по всем элементам.

Пакет предоставляет следующий набор статичных cache map:

| Класс                                                    | Описание                                                                |
| -------------------------------------------------------- | ----------------------------------------------------------------------- |
| `spaceonfire\BitrixTools\CacheMap\IblockCacheMap`        | позволяет получить информацию об инфоблоке по его символьному коду      |
| `spaceonfire\BitrixTools\CacheMap\HighloadBlockCacheMap` | позволяет получить информацию об HighLoad блоке по его названию         |
| `spaceonfire\BitrixTools\CacheMap\UserGroupCacheMap`     | позволяет получить информацию об группе по ее строковому идентификатора |

Для создания своего статичного cache map можно расширять класс `spaceonfire\BitrixTools\CacheMap\AbstractStaticCacheMap`

## Примеры

### Создаем Cache Map для справочника

Если в проекте есть некая сущность-справочник, для которой есть дата менеджер ORM Битрикс, можно добавить к ней функционал
cache map следующим образом:

```php
use Bitrix\Main\SystemException;
use spaceonfire\BitrixTools\CacheMap\CacheMap;
use spaceonfire\BitrixTools\CacheMap\CacheMapOptions;
use spaceonfire\BitrixTools\CacheMap\QueryCacheMap;

class MyDirectory extends Bitrix\Main\ORM\Data\DataManager
{
    private static $cacheMap;

    /**
     * Возвращает Cache Map для справочника
     * @return CacheMap
     */
    public static function getCacheMap(): CacheMap
    {
        try {
            if (self::$cacheMap === null) {
                self::$cacheMap = new QueryCacheMap(
                    self::query()->addSelect('*'),
                    new CacheMapOptions(self::getTableName(), 'ID', 'UF_XML_ID')
                );

            }

            return self::$cacheMap;
        } catch (SystemException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
```
