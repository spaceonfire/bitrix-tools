<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\Agents;

use CDatabase;
use CSite;
use RuntimeException;
use spaceonfire\BitrixTools\Common;
use Webmozart\Assert\Assert;

/**
 * Class AbstractAgent
 * @package spaceonfire\BitrixTools\Agents
 *
 * @method void run() Do agent job
 */
abstract class AbstractAgent implements Agent
{
    protected static function instantiate(): Agent
    {
        return new static();
    }

    final public static function agentName(): string
    {
        Assert::allScalar($args = func_get_args());

        $args = array_map(static function ($arg): string {
            return var_export($arg, true);
        }, $args);

        return sprintf('%s::agent(%s);', static::class, implode(', ', $args));
    }

    /**
     * @inheritDoc
     */
    public static function agent(): ?string
    {
        $instance = static::instantiate();

        if (!is_callable([$instance, 'run'])) {
            throw new RuntimeException(sprintf('Agent class %s must implements `run()` method', static::class));
        }

        $args = func_get_args();

        call_user_func_array([$instance, 'run'], $args);

        return call_user_func_array([static::class, 'agentName'], $args);
    }

    /**
     * @inheritDoc
     */
    public static function agentFields(): array
    {
        return [
            'MODULE_ID' => Common::getModuleIdByFqn(static::class),
            'USER_ID' => null,
            'SORT' => '0',
            'NAME' => call_user_func_array([static::class, 'agentName'], func_get_args()),
            'ACTIVE' => 'Y',
            'NEXT_EXEC' => date(CDatabase::DateFormatToPHP(CSite::GetDateFormat('FULL'))),
            'AGENT_INTERVAL' => static::INTERVAL_EVERY_DAY,
            'IS_PERIOD' => 'N',
        ];
    }
}
