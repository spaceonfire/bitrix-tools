# Агенты

Агенты в Битрикс позволяют делать отложенный запуск какого-то функционала в фоне.
Подробнее о технологии можно почитать в [курсе Битрикса][link-bitrix-agents].

## Пишем своего агента

При создании своих агентов рекомендуем создавать отдельные классы с использованием интерфейса
`spaceonfire\BitrixTools\Agents\Agent` и абстрактного класса `spaceonfire\BitrixTools\Agents\AbstractAgent`.

### Интерфейс

Интерфейс `Agent` предусматривает реализацию следующих статичных методов:

-   `public static function agent(): ?string` - сама функция агента, которая возвращает код следующего вызова или `null`;
-   `public static function agentFields(): array` - должен возвращать массив параметров для регистрации агента в БД
    функцией [`CAgent::Add()`][link-bxapi-cagent-add].

Так же в интерфейсе заведены константы для описания интервалов.

### Абстрактный класс

Абстрактный класс `AbstractAgent` реализует интерфейс `Agent`:

-   Метод `agentFields()` возвращает следующие параметры:

    -   `MODULE_ID` - ID модуля, сгенерированный из полного имени класса;
    -   `USER_ID` - `null`;
    -   `SORT` - `0`;
    -   `NAME` - сгенерированный код вызов метода `agent()`;
    -   `ACTIVE` - `Y`;
    -   `NEXT_EXEC` - текущая дата-время;
    -   `AGENT_INTERVAL` - `86400` - запуск агента раз в день;
    -   `IS_PERIOD` - `N` - при стандартных настройках, агенты с этим параметром выполняются на кроне.

-   Метод `agent()` выполняет работу агента:
    1. Создает объект класса агента (метод `instantiate()`);
    1. Выполняет метод `run()` от созданного объекта (метод должен быть реализован в классе созданного агента);
    1. Возвращает сгенерированный код вызов метода `agent()`, для следующего запуска агента.

### Изменение параметров агента

Для изменения параметров следует расширять метод `agentFields()`. Например, так:

```php
use spaceonfire\BitrixTools\Agents\AbstractAgent;

class MyAgent extends AbstractAgent
{
    /**
     * Do agent job
     */
    protected function run(): void
    {
        // TODO: Implement run() method.
    }

    public static function agentFields(): array
    {
        $fields = parent::agentFields();
        $fields['MODULE_ID'] = 'crm';
        $fields['AGENT_INTERVAL'] = self::INTERVAL_EVERY_HOUR * 2; // Запуск каждые 2 часа
        return $fields;
    }
}
```

### Предотвращаем повторный запуск агента

Чтобы реализовать одноразовый запуск агента или выход из цикла повторного запуска по условию, необходимо расширить метод
`agent()` и вернуть `null`, вместо кода следующего запуска.

```php
use spaceonfire\BitrixTools\Agents\AbstractAgent;

class MyAgent extends AbstractAgent
{
    /**
     * Do agent job
     */
    protected function run(): void
    {
        // TODO: Implement run() method.
    }

    public static function agent(): ?string
    {
        parent::agent();
        return null;
    }
}
```

### Объявление зависимостей через конструктор

Если по архитектуре требуется объявление зависимостей агента через конструктор, необходимо перегрузить метод `instantiate()`,
в котором создать экземпляр класса с нужными зависимостями.

```php
use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;
use spaceonfire\BitrixTools\Agents\AbstractAgent;
use spaceonfire\BitrixTools\Agents\Agent;

class MyAgent extends AbstractAgent
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * Constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Do agent job
     */
    protected function run(): void
    {
        $iblocks = $this->connection->query('select * from b_iblock')->fetchAll();
    }

    protected static function instantiate(): Agent
    {
        return new static(Application::getConnection());
    }
}
```

### Передача аргументов в агент

Абстрактный класс поддерживает передачу аргументов при вызове агента (поддерживаются только скалярные аргументы).
Обратите внимание, что при расширении методов `agent()` и `agentFields()`, для вызова родительских методов с передачей
своих аргументов, необходимо использовать особый синтаксис.

```php
use spaceonfire\BitrixTools\Agents\AbstractAgent;
use Webmozart\Assert\Assert;

class MyAgent extends AbstractAgent
{
    /**
     * Do agent job
     * @param int $a
     * @param int $b
     */
    protected function run(int $a, int $b): void
    {
        $c = $a + $b;
    }

    public static function agentFields(): array
    {
        $fields = call_user_func_array([static::class, 'parent::agentFields'], func_get_args());
        $fields['MODULE_ID'] = 'crm';
        $fields['AGENT_INTERVAL'] = self::INTERVAL_EVERY_HOUR * 2; // Запуск каждые 2 часа
        return $fields;
    }

    public static function agent(): ?string
    {
        call_user_func_array([static::class, 'parent::agent'], func_get_args());
        return null;
    }
}

Assert::same(MyAgent::agentName(1, 2), 'MyAgent::agent(1, 2)');
CAgent::Add(MyAgent::agentFields(1, 2));
```

[link-bitrix-agents]: https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=3436&LESSON_PATH=3913.4619.3436
[link-bxapi-cagent-add]: https://bxapi.ru/src/?module_id=main&name=CAgent::Add
