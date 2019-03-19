# ORM

Классы для работы с инфоблоками и highload-блоками через ORM

## Модель

В своем модуле создаете entity для конкретного инфоблока.
К примеру:

```
/local/
	modules/
	<ваш модуль>/
		content/
			news.php - Класс инфоблока
			newspropsimple.php - Класс свойств инфоблока
			newspropmultiple.php - Класс множественных свойств инфоблока
			newssection.php - Класс разделов инфоблока
```

Для того, чтоб классы могли знать друг о друге используется правило наименования:

```
<Сущность>Table
<Сущность>PropSimpleTable
<Сущность>PropMultipleTable
<Сущность>SectionTable
```

Все 4 класса обязательно должны лежать в одном `namespace`.
Еще одно обязательное условие, [свойства инфоблока должны находиться в отдельной таблице](http://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2723).

### Класс `NewsTable` инфоблока наследуем от `spaceonfire\BitrixTools\ORM\IblockElement`

```php
<?php

namespace Venor\Module\Content;

class NewsTable extends \spaceonfire\BitrixTools\ORM\IblockElement
{
	public static function getIblockCode(): string
	{
		return 'news';
	}
}
```

### Класс `NewsPropSimpleTable` свойств инфоблока наследуем от `spaceonfire\BitrixTools\ORM\IblockPropSimple`

```php
<?php

namespace Venor\Module\Content;

class NewsPropSimpleTable extends \spaceonfire\BitrixTools\ORM\IblockPropSimple
{
	public static function getIblockId(): int
	{
		return NewsTable::getIblockId();
	}
}
```

### Класс `NewsPropMultipleTable` множественных свойств инфоблока наследуем от `spaceonfire\BitrixTools\ORM\IblockPropMultiple`

```php
<?php

namespace Venor\Module\Content;

class NewsPropMultipleTable extends \spaceonfire\BitrixTools\ORM\IblockPropSimple
{
	public static function getIblockId(): int
	{
		return NewsTable::getIblockId();
	}
}
```

### Класс `NewsSectionTable` разделов инфоблока наследуем от `spaceonfire\BitrixTools\ORM\IblockSection`

```php
<?php

namespace Venor\Module\Content;

class NewsSectionTable extends \spaceonfire\BitrixTools\ORM\IblockSection
{
	public static function getIblockId(): int
	{
		return NewsTable::getIblockId();
	}
}
```

## Выборка

Во время выборки можно использовать все поля указанные в методе `getMap()`

### Доступны специальные поля в `getMap()`:

#### `\spaceonfire\BitrixTools\ORM\IblockElement`

```
DETAIL_PAGE_URL - формируется из настроек инфоблока

// Если есть класс с разделами то доступны ссылки на него
SECTION - \spaceonfire\BitrixTools\ORM\IblockSection
SECTIONS - Множественная привязка к разделам \spaceonfire\BitrixTools\ORM\IblockSection
```

#### `\spaceonfire\BitrixTools\ORM\IblockPropSimple`

```
IBLOCK_ELEMENT - Доступ к \spaceonfire\BitrixTools\ORM\IblockElement
```

#### `\spaceonfire\BitrixTools\ORM\IblockSection`

```
DETAIL_PAGE_URL - формируется из настроек инфоблока
PARENT_SECTION - Родительскй раздел \spaceonfire\BitrixTools\ORM\IblockSection
```

Для доступа к свойствам используются резервированные названия полей:

```
PROPERTY_SIMPLE.<символьный код свойства>
PROPERTY_MULTIPLE_<символьный код свойства>.VALUE
```

Примеры:

```php
<?php

use Venor\Module\Content\NewsTable;

$obNews = NewsTable::getList([
	'select' => [
		'ID',
		'NAME',
		'SOURCE_LINK' => 'PROPERTY_SIMPLE.SOURCE_LINK',
	],
	'filter' => [
		'=ACTIVE' => 'Y',
		'!PROPERTY_SIMPLE.SOURCE_LINK' => false,
	],
]);

while ($arNew = $obNews->fetch()) {
	// code
}
```

Примеры работы с множественными свойствами:

```php
<?php

use Venor\Module\Content\NewsTable;

$obNews = NewsTable::getList([
	'select' => [
		'ID',
		'NAME',
		'PHONES',
	],
	'runtime' => [
		'PHONES' => [
			'data_type' => 'string',
			'expression' => [
				'GROUP_CONCAT(%s)',
				'PROPERTY_MULTIPLE_PHONE.VALUE',
			],
		],
	],
	'filter' => [
		'=ACTIVE' => 'Y',
		'!PROPERTY_MULTIPLE_PHONE.VALUE' => false,
	],
]);

while ($arNew = $obNews->fetch()) {
	// code
}
```
