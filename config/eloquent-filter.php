<?php

return [
    /*
     * Default namespaces where filter and sort classes are located.
     * These namespaces are used when generating new classes or resolving
     * them dynamically in the Filterable/Sortable traits.
     *
     * Example usage:
     *   'filter' => 'App\\Filters\\'   -> App\Filters\UserFilter
     *   'sort'   => 'App\\Sorts\\'     -> App\Sorts\UserSort
     */
    'namespaces' => [
        'filter' => 'App\\Filters\\',
        'sort' => 'App\\Sorts\\',
    ],

    /*
     * Request key used to fetch filter parameters from the HTTP request.
     * Filters will be applied based on the array values under this key.
     *
     * Example request:
     *   GET /users?filter[name]=John&filter[age]=18
     *   In this case, request_filter_key = 'filter'
     */
    'request_filter_key' => 'filter',

    /*
     * Request key used to fetch sorting parameters from the HTTP request.
     * Sort parameters should be a string of column names, optionally prefixed
     * with '-' for descending order.
     *
     * Example request:
     *   GET /users?sort_by=name,-age
     *   In this case, request_sort_key = 'sort_by'
     */
    'request_sort_key' => 'sort_by',

    /*
     * Separator used to convert a string value into an array for filters.
     * Useful for multi-value filters like roles, tags, or IDs.
     *
     * Example:
     *   roles=1,2,3 -> ['1', '2', '3']
     */
    'array_value_separator' => ',',

    /*
     * Separator used to parse multiple sort columns from a string.
     *
     * Example:
     *   sort_by=name,-age -> ['name', '-age']
     */
    'sort_value_separator' => ',',
];
