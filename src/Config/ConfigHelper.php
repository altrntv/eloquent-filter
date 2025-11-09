<?php

namespace Altrntv\EloquentFilter\Config;

class ConfigHelper
{
    public static function namespace(): string
    {
        return config('eloquent-filter.namespace');
    }

    public static function requestFilterKey(): string
    {
        return config('eloquent-filter.request_filter_key');
    }

    public static function arrayValueSeparator(): string
    {
        return config('eloquent-filter.array_value_separator');
    }
}
