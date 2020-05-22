<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\Agents;

use CDatabase;
use CSite;
use RuntimeException;
use spaceonfire\BitrixTools\Common;

/**
 * Class AbstractAgent
 * @package spaceonfire\BitrixTools\Agents
 *
 * @method void run() Do agent job
 */
abstract class AbstractAgent implements Agent
{
    protected static function instantiate(): self
    {
        return new static();
    }

    final public static function agentName(): string
    {
        return static::class . '::agent();';
    }

    /**
     * @inheritDoc
     */
    public static function agent(): ?string
    {
        $instance = static::instantiate();

        if (!is_callable([$instance, 'run'])) {
            throw new RuntimeException(
                sprintf('Agent class %s must implements `run()` method', static::class)
            );
        }

        call_user_func_array([$instance, 'run'], func_get_args());

        return static::agentName();
    }

    /**
     * @inheritDoc
     */
    public static function agentFields(): array
    {
        return [
            'MODULE_ID' => Common::getModuleIdByFqn(static::class),
            'USER_ID' => NULL,
            'SORT' => '0',
            'NAME' => static::agentName(),
            'ACTIVE' => 'Y',
            'NEXT_EXEC' => date(CDatabase::DateFormatToPHP(CSite::GetDateFormat('FULL'))),
            'AGENT_INTERVAL' => static::INTERVAL_EVERY_DAY,
            'IS_PERIOD' => 'N',
        ];
    }
}
