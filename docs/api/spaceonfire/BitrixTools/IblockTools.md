# Class IblockTools

-   Full name: `\spaceonfire\BitrixTools\IblockTools`

## Methods

### buildSchema()

Собирает схему инфоблока, состоящую из полей элемента инфоблока и его свойств.

Принимает в качестве аргумента `$options` массив со следующими ключами:

```php
$options = [
    'IBLOCK_ID' => (int) ID инфоблока
    'DEFAULT_FIELDS' => (array) Массив полей по-умолчанию, известных заранее
    'EXCLUDE_FIELDS' => (array) Массив полей, которые необходимо исключить из итоговой схемы
]
```

| Param      | Type    | Description                                                                            |
| ---------- | ------- | -------------------------------------------------------------------------------------- |
| `$options` | _array_ |                                                                                        |
| **Return** | _array_ | Схема инфоблока - массив ассоциативных массивов, описывающих поля и свойства инфоблока |

```php
public static function IblockTools::buildSchema(mixed $options = []): array
```

File location: `src/IblockTools.php:51`

### disableIblockCacheClear()

Отключает сброс тэгированного кэша инфоблока

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
public static function IblockTools::disableIblockCacheClear(): bool
```

File location: `src/IblockTools.php:224`

### enableIblockCacheClear()

Включает сброс тэгированного кэша инфоблока

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
public static function IblockTools::enableIblockCacheClear(): bool
```

File location: `src/IblockTools.php:236`

### getElementMeta()

Возвращает SEO мета-данные для элемента инфоблока по ID

| Param        | Type    | Description  |
| ------------ | ------- | ------------ |
| `$iblockId`  | _int_   | ID инфоблока |
| `$elementId` | _int_   | ID элемента  |
| **Return**   | _array_ |              |

```php
public static function IblockTools::getElementMeta(int $iblockId, int $elementId): array
```

File location: `src/IblockTools.php:482`

### getEnumIdByXmlId()

Возвращает id значения enum свойства по XML_ID

| Param           | Type            | Description                                                       |
| --------------- | --------------- | ----------------------------------------------------------------- |
| `$iblockId`     | _int_           | ID инфоблока                                                      |
| `$xml`          | _string_        | - XML_ID значения. Если передан `null`, будет возвращено значение |
| по-умолчанию    |
| `$propertyCode` | _string_        | - Символьный код свойства                                         |
| **Return**      | _int&#124;null_ |                                                                   |

```php
public static function IblockTools::getEnumIdByXmlId(int $iblockId, ?string $xml, string $propertyCode): ?int
```

File location: `src/IblockTools.php:437`

### getEnumValueById()

Возвращает значение enum свойства по id

| Param           | Type               | Description                                                              |
| --------------- | ------------------ | ------------------------------------------------------------------------ |
| `$iblockId`     | _int_              | ID инфоблока                                                             |
| `$id`           | _int&#124;null_    | ID значения. Если передан `null`, будет возвращено значение по-умолчанию |
| `$propertyCode` | _string_           | Символьный код свойства                                                  |
| **Return**      | _string&#124;null_ |                                                                          |

```php
public static function IblockTools::getEnumValueById(int $iblockId, ?int $id, string $propertyCode): ?string
```

File location: `src/IblockTools.php:402`

### getEnumValueByXmlId()

Возвращает значение enum свойства по его xml id

| Param           | Type               | Description                                                     |
| --------------- | ------------------ | --------------------------------------------------------------- |
| `$iblockId`     | _int_              | ID инфоблока                                                    |
| `$xml`          | _string&#124;null_ | XML_ID значения. Если передан `null`, будет возвращено значение |
| по-умолчанию    |
| `$propertyCode` | _string_           | Символьный код свойства                                         |
| **Return**      | _string&#124;null_ |                                                                 |

```php
public static function IblockTools::getEnumValueByXmlId(int $iblockId, ?string $xml, string $propertyCode): ?string
```

File location: `src/IblockTools.php:423`

### getEnumXmlIdById()

Возвращает xml_id значения enum свойства по id

| Param           | Type               | Description                                                              |
| --------------- | ------------------ | ------------------------------------------------------------------------ |
| `$iblockId`     | _int_              | ID инфоблока                                                             |
| `$id`           | _int&#124;null_    | ID значения. Если передан `null`, будет возвращено значение по-умолчанию |
| `$propertyCode` | _string_           | Символьный код свойства                                                  |
| **Return**      | _string&#124;null_ |                                                                          |

```php
public static function IblockTools::getEnumXmlIdById(int $iblockId, ?int $id, string $propertyCode): ?string
```

File location: `src/IblockTools.php:463`

### getEnums()

Возвращает значения всех свойств типа "список"

| Param       | Type            | Description                                                                          |
| ----------- | --------------- | ------------------------------------------------------------------------------------ |
| `$iblockId` | _int&#124;null_ | ID инфоблока. Если передан `null`, будут возвращены все свойства, сгруппированные по |
| инфоблокам  |
| **Return**  | _array_         |                                                                                      |

```php
public static function IblockTools::getEnums(?int $iblockId = null): array
```

File location: `src/IblockTools.php:351`

### getIblockIdByCode()

Возвращает ID инфоблока по символьному коду

| Param      | Type            | Description |
| ---------- | --------------- | ----------- |
| `$code`    | _string_        |             |
| **Return** | _null&#124;int_ |             |

```php
public static function IblockTools::getIblockIdByCode(string $code): ?int
```

File location: `src/IblockTools.php:29`

### getProperties()

Возвращает список свойств для инфоблока

| Param       | Type    | Description  |
| ----------- | ------- | ------------ |
| `$iblockId` | _int_   | ID инфоблока |
| **Return**  | _array_ |              |

```php
public static function IblockTools::getProperties(int $iblockId): array
```

File location: `src/IblockTools.php:291`

### getPropertyCodeById()

Возвращает символьный код свойства по его ID

| Param       | Type               | Description  |
| ----------- | ------------------ | ------------ |
| `$iblockId` | _int_              | ID инфоблока |
| `$id`       | _int_              | ID свойства  |
| **Return**  | _string&#124;null_ |              |

```php
public static function IblockTools::getPropertyCodeById(int $iblockId, int $id): ?string
```

File location: `src/IblockTools.php:321`

### getPropertyIdByCode()

Возвращает ID свойства по его коду

| Param       | Type            | Description             |
| ----------- | --------------- | ----------------------- |
| `$iblockId` | _int_           | ID инфоблока            |
| `$code`     | _string_        | Символьный код свойства |
| **Return**  | _int&#124;null_ |                         |

```php
public static function IblockTools::getPropertyIdByCode(int $iblockId, string $code): ?int
```

File location: `src/IblockTools.php:338`

### getSectionMeta()

Возвращает SEO мета-данные для раздела инфоблока по ID

| Param        | Type    | Description  |
| ------------ | ------- | ------------ |
| `$iblockId`  | _int_   | ID инфоблока |
| `$sectionId` | _int_   | ID раздела   |
| **Return**   | _array_ |              |

```php
public static function IblockTools::getSectionMeta(int $iblockId, int $sectionId): array
```

File location: `src/IblockTools.php:493`

### getSectionsTree()

Возвращает список разделов инфоблока, выравненные по вложенности точками

| Param         | Type    | Description                                |
| ------------- | ------- | ------------------------------------------ |
| `$iblockId`   | _int_   | ID инфоблока                               |
| `$parameters` | _array_ | дополнительные параметры запроса           |
| **Return**    | _array_ | Массив вида `[SECTION_ID => SECTION_NAME]` |

```php
public static function IblockTools::getSectionsTree(int $iblockId, array $parameters = []): array
```

File location: `src/IblockTools.php:250`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)
