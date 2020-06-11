# Class CacheMapOptions

-   Full name: `\spaceonfire\BitrixTools\CacheMap\CacheMapOptions`

## Methods

### \_\_construct()

CacheMapOptions constructor.

| Param              | Type     | Description |
| ------------------ | -------- | ----------- |
| `$id`              | _string_ |             |
| `$idKey`           | _string_ |             |
| `$codeKey`         | _string_ |             |
| `$isCaseSensitive` | _bool_   |             |
| `$cacheOptions`    | _array_  |             |

```php
public function CacheMapOptions::__construct(string $id, string $idKey = 'ID', string $codeKey = 'CODE', bool $isCaseSensitive = false, ?array $cacheOptions = null): mixed
```

File location: `src/CacheMap/CacheMapOptions.php:38`

### getCacheOptions()

Getter for `cacheOptions` property

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _array_ |             |

```php
public function CacheMapOptions::getCacheOptions(): array
```

File location: `src/CacheMap/CacheMapOptions.php:83`

### getCodeKey()

Getter for `codeKey` property

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _string_ |             |

```php
public function CacheMapOptions::getCodeKey(): string
```

File location: `src/CacheMap/CacheMapOptions.php:65`

### getIdKey()

Getter for `idKey` property

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _string_ |             |

```php
public function CacheMapOptions::getIdKey(): string
```

File location: `src/CacheMap/CacheMapOptions.php:56`

### isCaseSensitive()

Getter for `isCaseSensitive` property

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
public function CacheMapOptions::isCaseSensitive(): bool
```

File location: `src/CacheMap/CacheMapOptions.php:74`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)