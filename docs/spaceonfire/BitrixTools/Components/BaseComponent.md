# Class BaseComponent

Базовый компонент

-   Full name: `\spaceonfire\BitrixTools\Components\BaseComponent`
-   This class uses: `\spaceonfire\BitrixTools\Components\CommonComponentTrait`

## Methods

### abortCache()

Сброс кэширования.

```php
public function CommonComponentTrait::abortCache(): void
```

File location: `src/Components/BaseComponent.php:242`

### addCacheAdditionalId()

Добавиляет дополнительный ID для кэша

| Param | Type    | Description |
| ----- | ------- | ----------- |
| `$id` | _mixed_ |             |

```php
public function CommonComponentTrait::addCacheAdditionalId(mixed $id): void
```

File location: `src/Components/BaseComponent.php:372`

### catchError()

Вызывается при возникновении ошибки

Сбрасывает кэш, показывает сообщение об ошибке (в общем виде для пользователей и детально
для админов), пишет ошибку в лог Битрикса

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$throwable` | _\Throwable_ |             |

```php
protected function CommonComponentTrait::catchError(\Throwable $throwable): mixed
```

File location: `src/Components/BaseComponent.php:306`

### executeComponent()

Выполнение компонента

| Param      | Type     | Description                  |
| ---------- | -------- | ---------------------------- |
| **Return** | _static_ | возвращает объект компонента |

```php
public function BaseComponent::executeComponent(): mixed
```

File location: `src/Components/BaseComponent.php:55`

### executeEpilog()

Выполняется после получения результатов. Не кэшируется

```php
protected function CommonComponentTrait::executeEpilog(): mixed
```

File location: `src/Components/BaseComponent.php:263`

### executeMain()

Основная логика компонента.

Результат работы метода будет закэширован.

```php
protected function CommonComponentTrait::executeMain(): mixed
```

File location: `src/Components/BaseComponent.php:251`

### executeProlog()

Выполняется до получения результатов. Не кэшируется

```php
protected function CommonComponentTrait::executeProlog(): mixed
```

File location: `src/Components/BaseComponent.php:200`

### getParentParam()

Возвращает значение параметра родительского компонента

| Param        | Type              | Description |
| ------------ | ----------------- | ----------- |
| `$paramName` | _string_          |             |
| **Return**   | _mixed&#124;null_ |             |

```php
protected function CommonComponentTrait::getParentParam(string $paramName): mixed
```

File location: `src/Components/BaseComponent.php:150`

### includeModules()

Загружает модули 1С-Битрикс.

```php
public function CommonComponentTrait::includeModules(): void
```

File location: `src/Components/BaseComponent.php:69`

### init()

Инициализация компонента.

Метод вызывается после вызова конструктора и подключения необходимых модулей.
Служит для выполнения дополнительных настроек.

```php
protected function CommonComponentTrait::init(): mixed
```

File location: `src/Components/BaseComponent.php:83`

### isAjax()

Проверяет отправлен ли запрос через AJAX

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
public function CommonComponentTrait::isAjax(): bool
```

File location: `src/Components/BaseComponent.php:350`

### onIncludeComponentLang()

Загружает файлы переводов компонента (component.php и class.php)

```php
public function CommonComponentTrait::onIncludeComponentLang(): void
```

File location: `src/Components/BaseComponent.php:59`

### registerCacheTag()

Регистрирует тэг в кэше

| Param  | Type     | Description |
| ------ | -------- | ----------- |
| `$tag` | _string_ |             |

```php
public static function CommonComponentTrait::registerCacheTag(string $tag): void
```

File location: `src/Components/BaseComponent.php:361`

### render()

Ренедеринг шаблона компонента

```php
public function CommonComponentTrait::render(): mixed
```

File location: `src/Components/BaseComponent.php:283`

### return404()

Выбрасывает NotFoundException

| Param        | Type                   | Description                                                                 |
| ------------ | ---------------------- | --------------------------------------------------------------------------- |
| `$throwable` | _\Throwable&#124;null_ | Исходное исключение. При наличии будет использовано его сообщение об ошибке |

```php
public function CommonComponentTrait::return404(?\Throwable $throwable = null): mixed
```

File location: `src/Components/BaseComponent.php:293`

### run()

Универсальный флоу выполнения компонента

```php
final protected function BaseComponent::run(): void
```

File location: `src/Components/BaseComponent.php:25`

### showExceptionAdmin()

Отображат сообщение об ошибке для админов

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$throwable` | _\Throwable_ |             |

```php
protected function CommonComponentTrait::showExceptionAdmin(\Throwable $throwable): mixed
```

File location: `src/Components/BaseComponent.php:340`

### showExceptionUser()

Отображат сообщение об ошибке для пользователей

| Param        | Type         | Description |
| ------------ | ------------ | ----------- |
| `$throwable` | _\Throwable_ |             |

```php
protected function CommonComponentTrait::showExceptionUser(\Throwable $throwable): mixed
```

File location: `src/Components/BaseComponent.php:331`

### startCache()

Инициализация кэширования

| Param      | Type   | Description |
| ---------- | ------ | ----------- |
| **Return** | _bool_ |             |

```php
public function CommonComponentTrait::startCache(): bool
```

File location: `src/Components/BaseComponent.php:208`

### triggerEvent()

Вызывает событие, специфичное для компонента

| Param      | Type                             | Description                                                          |
| ---------- | -------------------------------- | -------------------------------------------------------------------- |
| `$type`    | _string_                         | Тип события. Имя класса компонента будет добавлено ввиде префикса.   |
| `$params`  | _array_                          | Параметры события. Параметр `component` будет добавлен автоматически |
| `$filter`  | _null&#124;string&#124;string[]_ | Фильтр события                                                       |
| **Return** | _\Bitrix\Main\Event_             |                                                                      |

```php
public function CommonComponentTrait::triggerEvent(string $type, array $params = [], mixed $filter = null): \spaceonfire\BitrixTools\Components\Bitrix\Main\Event
```

File location: `src/Components/BaseComponent.php:384`

### writeCache()

Записывает результат кэширования на диск.

```php
public function CommonComponentTrait::writeCache(): void
```

File location: `src/Components/BaseComponent.php:234`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)
