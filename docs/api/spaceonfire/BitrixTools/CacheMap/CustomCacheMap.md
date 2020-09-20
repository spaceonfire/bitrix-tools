# Class CustomCacheMap

Класс CustomCacheMap позволяет создать собственный кэшированный справочник

-   Full name: `\spaceonfire\BitrixTools\CacheMap\CustomCacheMap`
-   Parent class: `\spaceonfire\BitrixTools\CacheMap\AbstractCacheMapDecorator`
-   This class implements: `\spaceonfire\BitrixTools\CacheMap\CacheMap`

## Methods

### \_\_construct()

Создает собственный кэшированный справочник на основе предоставленного источника данных.

**ВАЖНО**: Позаботьтесь самостоятельно об очистке кэша, при изменении данных!

| Param                    | Type                                                    | Description                                                            |
| ------------------------ | ------------------------------------------------------- | ---------------------------------------------------------------------- |
| `$dataSource`            | _\Bitrix\Main\ORM\Query\Query&#124;callable&#124;array_ | Источник данных, можно передать объект запроса ORM, массив или функцию |
| возвращающую `iterable`. |
| `$options`               | _\spaceonfire\BitrixTools\CacheMap\CacheMapOptions_     | Настройки                                                              |

```php
public function CustomCacheMap::__construct(mixed $dataSource, \spaceonfire\BitrixTools\CacheMap\CacheMapOptions $options): mixed
```

File location: `src/CacheMap/CustomCacheMap.php:25`

### clearCache()

```php
public function AbstractCacheMapDecorator::clearCache(): void
```

File location: `src/CacheMap/AbstractCacheMapDecorator.php:44`

### get()

```php
public function AbstractCacheMapDecorator::get(mixed $code): mixed
```

File location: `src/CacheMap/AbstractCacheMapDecorator.php:28`

### getId()

```php
public function AbstractCacheMapDecorator::getId(mixed $code): mixed
```

File location: `src/CacheMap/AbstractCacheMapDecorator.php:36`

### getIterator()

```php
public function AbstractCacheMapDecorator::getIterator(): \Traversable
```

File location: `src/CacheMap/AbstractCacheMapDecorator.php:52`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)