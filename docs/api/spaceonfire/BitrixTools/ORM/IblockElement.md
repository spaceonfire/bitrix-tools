# Class IblockElement

-   Full name: `\spaceonfire\BitrixTools\ORM\IblockElement`

## Methods

### getCount()

```php
public static function IblockElement::getCount(mixed $filter = [], array $cache = []): mixed
```

File location: `src/ORM/IblockElement.php:235`

### getElementMeta()

Возвращает SEO мета-данные для элемента инфоблока по ID

| Param        | Type    | Description |
| ------------ | ------- | ----------- |
| `$elementId` | _int_   | ID элемента |
| **Return**   | _array_ |             |

```php
public static function IblockElement::getElementMeta(int $elementId): array
```

File location: `src/ORM/IblockElement.php:310`

### getEnumIdByXmlId()

Возвращает id значения enum свойства по XML_ID

| Param           | Type            | Description               |
| --------------- | --------------- | ------------------------- |
| `$xml`          | _string_        | - xml_id property value   |
| `$propertyCode` | _string_        | - Character property code |
| **Return**      | _int&#124;null_ |                           |

```php
public static function IblockElement::getEnumIdByXmlId(mixed $xml, mixed $propertyCode): ?int
```

File location: `src/ORM/IblockElement.php:259`

### getEnumValueById()

Возвращает значение enum свойства по id

| Param           | Type               | Description                                                   |
| --------------- | ------------------ | ------------------------------------------------------------- |
| `$id`           | _int&#124;null_    | ID значения, если null будет возвращено значение по-умолчанию |
| `$propertyCode` | _string_           | Символьный код свойства                                       |
| **Return**      | _string&#124;null_ |                                                               |

```php
public static function IblockElement::getEnumValueById(?int $id, string $propertyCode): ?string
```

File location: `src/ORM/IblockElement.php:247`

### getIblockCode()

Возвращает символьный код инфоблока.

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _string_ |             |

```php
public static function IblockElement::getIblockCode(): string
```

File location: `src/ORM/IblockElement.php:45`

### getIblockId()

Возвращает ID инфоблока

Если Вам заранее известен ID инфоблока, лучше самостоятельно возвращать его в переопределении
метода. Иначе следует переопределить метод `getIblockCode()`.

| Param      | Type  | Description |
| ---------- | ----- | ----------- |
| **Return** | _int_ |             |

```php
public static function IblockElement::getIblockId(): int
```

File location: `src/ORM/IblockElement.php:25`

### getList()

```php
public static function IblockElement::getList(array $parameters = []): mixed
```

File location: `src/ORM/IblockElement.php:226`

### getMap()

Возвращает схему полей сущности

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _array_ |             |

```php
public static function IblockElement::getMap(): array
```

File location: `src/ORM/IblockElement.php:64`

### getProperties()

| Param      | Type               | Description |
| ---------- | ------------------ | ----------- |
| **Return** | _array&#124;mixed_ |             |

```php
protected static function IblockElement::getProperties(): mixed
```

File location: `src/ORM/IblockElement.php:280`

### getPropertyCodeById()

Возвращает символьный код свойства по его ID

| Param      | Type               | Description |
| ---------- | ------------------ | ----------- |
| `$id`      | _int_              | ID свойства |
| **Return** | _null&#124;string_ |             |

```php
public static function IblockElement::getPropertyCodeById(int $id): ?string
```

File location: `src/ORM/IblockElement.php:290`

### getPropertyIdByCode()

Возвращает ID свойства по его коду

| Param      | Type            | Description             |
| ---------- | --------------- | ----------------------- |
| `$code`    | _string_        | Символьный код свойства |
| **Return** | _null&#124;int_ |                         |

```php
public static function IblockElement::getPropertyIdByCode(string $code): ?int
```

File location: `src/ORM/IblockElement.php:300`

### getTableName()

Возвращает название таблицы для сущности в БД

| Param      | Type     | Description |
| ---------- | -------- | ----------- |
| **Return** | _string_ |             |

```php
public static function IblockElement::getTableName(): string
```

File location: `src/ORM/IblockElement.php:55`

### getXmlIdById()

Возвращает xml_id значения enum свойства по id

| Param           | Type                        | Description                                                   |
| --------------- | --------------------------- | ------------------------------------------------------------- |
| `$id`           | _int&#124;null_             | ID значения, если null будет возвращено значение по-умолчанию |
| `$propertyCode` | _string_                    | Символьный код свойства                                       |
| **Return**      | _string&#124;int&#124;null_ |                                                               |

```php
public static function IblockElement::getXmlIdById(?int $id, string $propertyCode): mixed
```

File location: `src/ORM/IblockElement.php:271`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)
