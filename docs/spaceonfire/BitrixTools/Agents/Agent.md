# Interface Agent

-   Full name: `\spaceonfire\BitrixTools\Agents\Agent`

## Constants

| Constant               | Value      | Description |
| ---------------------- | ---------- | ----------- |
| `INTERVAL_EVERY_HOUR`  | `3600`     |             |
| `INTERVAL_EVERY_DAY`   | `86400`    |             |
| `INTERVAL_EVERY_WEEK`  | `604800`   |             |
| `INTERVAL_EVERY_MONTH` | `2629744`  |             |
| `INTERVAL_EVERY_YEAR`  | `31557600` |             |

## Methods

### agent()

Bitrix Agent

| Param      | Type               | Description                               |
| ---------- | ------------------ | ----------------------------------------- |
| **Return** | _string&#124;null_ | agent method call code for next execution |

```php
public static function Agent::agent(): ?string
```

File location: `src/Agents/Agent.php:21`

### agentFields()

Fields to be pass to `\CAgent::Add($fields)` as `$fields`

| Param      | Type    | Description |
| ---------- | ------- | ----------- |
| **Return** | _array_ |             |

```php
public static function Agent::agentFields(): array
```

File location: `src/Agents/Agent.php:28`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)