<?php

namespace Altrntv\EloquentFilter\Config;

class ConfigHelper
{
    public static function filterNamespace(): string
    {
        return config('eloquent-filter.namespaces.filter');
    }

    public static function sortNamespace(): string
    {
        return config('eloquent-filter.namespaces.sort');
    }

    public static function requestFilterKey(): string
    {
        return config('eloquent-filter.request_filter_key');
    }

    public static function requestSortKey(): string
    {
        return config('eloquent-filter.request_sort_key');
    }

    public static function arrayValueSeparator(): string
    {
        return config('eloquent-filter.array_value_separator');
    }

    public static function sortValueSeparator(): string
    {
        return config('eloquent-filter.sort_value_separator');
    }
}
