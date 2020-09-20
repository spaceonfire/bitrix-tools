# Class BaseController

-   Full name: `\spaceonfire\BitrixTools\Controllers\BaseController`
-   This class implements: `\spaceonfire\BitrixTools\Controllers\ControllerInterface`

## Methods

### \_\_construct()

Создает новый контроллер

```php
public function BaseController::__construct(): mixed
```

File location: `src/Controllers/BaseController.php:36`

### doAction()

Выполняет действие контроллера

| Param   | Type     | Description  |
| ------- | -------- | ------------ |
| `$name` | _string_ | Имя действия |

```php
public function BaseController::doAction(mixed $name): void
```

File location: `src/Controllers/BaseController.php:76`

### factory()

"Фабрика" контроллеров

| Param        | Type     | Description      |
| ------------ | -------- | ---------------- |
| `$name`      | _string_ | Имя сущности     |
| `$namespace` | _string_ | Неймспейс класса |
| **Return**   | _static_ |                  |

```php
public static function BaseController::factory(mixed $name, mixed $namespace = __NAMESPACE__): \spaceonfire\BitrixTools\Controllers\ControllerInterface
```

File location: `src/Controllers/BaseController.php:48`

### getComponent()

Возвращает код, сгенерированный компонентом Битрикс

| Param              | Type     | Description                      |
| ------------------ | -------- | -------------------------------- |
| `$name`            | _string_ | Имя компонента                   |
| `$template`        | _string_ | Шаблон компонента                |
| `$params`          | _array_  | Параметры компонента             |
| `$componentResult` | _mixed_  | Данные, возвращаемые компонентом |
| **Return**         | _string_ |                                  |

```php
protected function BaseController::getComponent(mixed $name, mixed $template = '', mixed $params = [], mixed &$componentResult = null): string
```

File location: `src/Controllers/BaseController.php:117`

### getIncludeArea()

Возвращает код, сгенерированный включаемой областью Битрикс

| Param             | Type     | Description                               |
| ----------------- | -------- | ----------------------------------------- |
| `$path`           | _string_ | Путь до включаемой области                |
| `$params`         | _array_  | Массив параметров для подключаемого файла |
| `$functionParams` | _array_  | Массив настроек данного метода            |
| **Return**        | _string_ |                                           |

```php
protected function BaseController::getIncludeArea(mixed $path, mixed $params = [], mixed $functionParams = []): string
```

File location: `src/Controllers/BaseController.php:132`

### getParam()

Возвращает значение входного параметра

| Param      | Type     | Description           |
| ---------- | -------- | --------------------- |
| `$name`    | _string_ | Имя параметра         |
| `$default` | _mixed_  | Значение по-умолчанию |
| **Return** | _mixed_  |                       |

```php
public function BaseController::getParam(string $name, mixed $default = null): mixed
```

File location: `src/Controllers/BaseController.php:171`

### getRequest()

Геттер для свойства `request`

| Param      | Type                       | Description |
| ---------- | -------------------------- | ----------- |
| **Return** | _\Bitrix\Main\HttpRequest_ |             |

```php
public function BaseController::getRequest(): \Bitrix\Main\HttpRequest
```

File location: `src/Controllers/BaseController.php:194`

### getView()

Геттер для свойства `view`

| Param      | Type                                                     | Description |
| ---------- | -------------------------------------------------------- | ----------- |
| **Return** | _\spaceonfire\BitrixTools\Views\ViewInterface&#124;null_ |             |

```php
public function BaseController::getView(): ?\spaceonfire\BitrixTools\Views\ViewInterface
```

File location: `src/Controllers/BaseController.php:203`

### hasParam()

Проверяет существования параметра по имени

| Param      | Type     | Description   |
| ---------- | -------- | ------------- |
| `$name`    | _string_ | Имя параметра |
| **Return** | _bool_   |               |

```php
public function BaseController::hasParam(string $name): bool
```

File location: `src/Controllers/BaseController.php:185`

### setParams()

Сеттер для свойства `params`

| Param     | Type    | Description |
| --------- | ------- | ----------- |
| `$params` | _array_ |             |
| `$merge`  | _bool_  |             |

```php
public function BaseController::setParams(array $params, bool $merge = true): void
```

File location: `src/Controllers/BaseController.php:144`

### setParamsPairs()

Устанавливает параметры из пар в массиве

| Param    | Type       | Description             |
| -------- | ---------- | ----------------------- |
| `$pairs` | _string[]_ | Пары \[ключ]\[значение] |

```php
public function BaseController::setParamsPairs(mixed $pairs): void
```

File location: `src/Controllers/BaseController.php:157`

### setView()

Сеттер для свойства `view`

| Param      | Type                                                     | Description |
| ---------- | -------------------------------------------------------- | ----------- |
| `$view`    | _\spaceonfire\BitrixTools\Views\ViewInterface&#124;null_ |             |
| **Return** | _static_                                                 |             |

```php
public function BaseController::setView(?\spaceonfire\BitrixTools\Views\ViewInterface $view): \spaceonfire\BitrixTools\Controllers\ControllerInterface
```

File location: `src/Controllers/BaseController.php:213`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)
