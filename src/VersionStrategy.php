<?php

namespace App;

class VersionStrategy
{
    public const STRATEGY_FIXED = 'fixed';
    public const STRATEGY_PATCH = 'patch';
    public const STRATEGY_MINOR = 'minor';
    public const STRATEGY_MAJOR = 'major';
    public const STRATEGIES = [
        self::STRATEGY_FIXED,
        self::STRATEGY_PATCH,
        self::STRATEGY_MINOR,
        self::STRATEGY_MAJOR,
    ];
}
