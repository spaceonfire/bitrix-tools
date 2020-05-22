<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\Agents;

use CAgent;

interface Agent
{
    public const INTERVAL_EVERY_HOUR = 3600;
    public const INTERVAL_EVERY_DAY = 86400;
    public const INTERVAL_EVERY_WEEK = 604800;
    public const INTERVAL_EVERY_MONTH = 2629744;
    public const INTERVAL_EVERY_YEAR = 31557600;

    /**
     * Bitrix Agent
     * @return string|null agent method call code for next execution
     */
    public static function agent(): ?string;

    /**
     * Fields to be pass to `\CAgent::Add($fields)` as `$fields`
     * @return array
     * @see CAgent::Add()
     */
    public static function agentFields(): array;
}
