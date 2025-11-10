# Eloquent Filter

[![Latest Version](https://img.shields.io/github/release/altrntv/eloquent-filter.svg?style=flat-square)](https://github.com/altrntv/eloquent-filter/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/altrntv/eloquent-filter.svg?style=flat-square)](https://packagist.org/packages/altrntv/eloquent-filter)

Eloquent Filter provides a clean and expressive way to apply dynamic, request-driven filters to your Eloquent models.
It automatically maps incoming request parameters to filter methods, making complex query filtering simple and maintainable.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Basic Usage Filters](#basic-usage-filters)
  - [Creating a Filter](#creating-a-filter-class)
  - [Controller Example](#controller-example)
  - [Validating Filters](#validating-filters)
  - [Casting Filter Values](#casting-filter-values)
  - [Joining Parameters](#joining-parameters)
- [Basic Usage Sorts](#basic-usage-sorts)
  - [Creating a Sort Class](#creating-a-sort-class)
  - [Request Example](#request-example)
  - [Applying Sort](#applying-sort)
- [Testing](#testing)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Requirements

This package requires:

* PHP ^8.3
* Laravel ^12.0

## Installation

Install the package via Composer:

```bash
composer require altrntv/eloquent-filter
```

## Basic Usage Filters

Extend your model with the `Filterable` trait:

```php
use Altrntv\EloquentFilter\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @method static Builder<static>|self filter(array<string, mixed> $parameters)
 * @method static Builder<static>|self filterByRequest()
 */
class User extends Authenticatable
{
    use Filterable;
}
```

After adding the trait, your model gains two new query builder methods:

* `filter(array $parameters)` — Applies filters using a key-value array.
* `filterByRequest()` — Automatically applies filters from the current HTTP request, using the key defined in your configuration.

---

### Creating a Filter Class

You can create a filter class using the Artisan command:

```bash
php artisan make:eloquent-filter UserFilter
```

Then, define methods corresponding to each filterable parameter.
Each method should accept a value and modify the query builder accordingly.

```php
use Altrntv\EloquentFilter\Filters\EloquentFilter;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends EloquentFilter
{
    public function name(string $value): Builder
    {
        return $this->builder
            ->whereLike('name', "%{$value}%");
    }

    public function age(string $value): Builder
    {
        return $this->builder
            ->where('age', '>=', $value);
    }
}
```

---

### Controller Example

```php
use App\Models\User;
use App\Http\Requests\UserIndexRequest;
use Illuminate\Http\JsonResponse;

final class UserController extends Controller
{
    public function index(UserIndexRequest $request): JsonResponse
    {
        $users = User::query()
            ->filterByRequest()
            ->get();
    
        return $users->toResourceCollection();
    }
}
```

---

### Validating Filters

It’s recommended to validate incoming filter parameters in your request class:

```php
use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'filter.name' => ['string', 'max:255'],
            'filter.age' => ['integer', 'min:0', 'max:150'],
        ];
    }
}
```

--- 

### Casting Filter Values

Eloquent Filter supports the following cast types:
`integer`, `string`, `boolean`, and `array`.

To cast a value, define it in the $casts property of your filter:

```php
use Altrntv\EloquentFilter\Filters\EloquentFilter;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends EloquentFilter
{
    protected array $casts = [
        'age' => 'integer',
        'roles' => 'array',
    ];

    public function age(int $value): Builder
    {
        return $this->builder
            ->where('age', '>=', $value);
    }

    public function roles(array $value): Builder
    {
        return $this->builder
            ->whereIn('roles', $value);
    }
}
```

And in your form request:

```php
use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'filter.name' => ['string', 'max:255'],
            'filter.age' => ['integer', 'min:0', 'max:150'],
            'filter.roles' => ['string', 'regex:/^\d+(,\d+)*$/'],
        ];
    }
}
```

A typical request might look like this:

```
GET /users?filter[name]=John&filter[age]=18&filter[roles]=1,2,4
```

---

### Joining Parameters

Sometimes multiple request parameters represent a single logical concept.
For example, `vip_at_from` and `vip_at_to` form a date range.

Eloquent Filter allows you to group multiple parameters under a single key using the `$joinParameters` property.

All keys inside the group are:

* **converted to camelCase** (e.g., `vip_at_from` → `vipAtFrom`),
* **passed to your filter method via argument unpacking (`...`)**, so the method’s argument names must match the camelCased keys.

#### Example Filter Class

```php
use Altrntv\EloquentFilter\Filters\EloquentFilter;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends EloquentFilter
{
    protected array $joinParameters = [
        'vip_at' => ['vip_at_from', 'vip_at_to'],
    ];

    public function vipAt(string $vipAtFrom, string $vipAtTo): Builder
    {
        return $this->builder
            ->where(function (Builder $query) use ($vipAtFrom, $vipAtTo) {
                $query
                    ->whereDate('vip_from', '<=', $vipAtTo)
                    ->whereDate('vip_to', '>=', $vipAtFrom);
            });;
    }
}
```

#### Request Example

```
GET /users?filter[vip_at_from]=2024-01-01&filter[vip_at_to]=2024-12-31
```

#### Transformed Parameters

```php
$this->parameters = [
    'vip_at' => [
        'vipAtFrom' => '2024-01-01',
        'vipAtTo' => '2024-12-31',
    ],
]
```

_This approach is especially useful for handling ranges or multipart filters, keeping your request parameters clean and your filter methods readable._

## Basic Usage Sorts

Eloquent Filter now supports sorting via the `Sortable` trait and `EloquentSort` classes.
This allows you to apply dynamic sorting based on HTTP requests or string parameters.

Extend your model with the `Sortable` trait:

```php
use Altrntv\EloquentFilter\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @method static Builder<static>|self sort(string $parameters)
 * @method static Builder<static>|self sortByRequest()
 */
class User extends Authenticatable
{
    use Sortable;
}
```

After adding the trait, your model gains two query builder methods:

* `sort(string $parameters)` — Apply sorting using a comma-separated string of columns.
* `sortByRequest()` — Automatically applies sorting from the current HTTP request, using the key defined in your configuration.

---

### Creating a Sort Class

You can create a sort class using the Artisan command:

```bash
php artisan make:eloquent-sort UserSort
```

```php
use Altrntv\EloquentFilter\Filters\EloquentSort;
use Illuminate\Database\Eloquent\Builder;

class UserSort extends EloquentSort
{
    public function name(string $direction): Builder
    {
        return $this->builder
            ->orderBy('name', $direction);
    }

    public function age(string $direction): Builder
    {
        return $this->builder
            ->orderBy('age', $direction);
    }
}
```

---

### Request Example

```
GET /users?sort_by=name,-age
```

* `name` — ascending
* `-age` — descending

The sort string is split by the configured separator (`,` by default), and the direction is inferred from the `-` prefix.

---

### Applying Sort

In controller:
```php
$users = User::query()
    ->sortByRequest()
    ->get();
```

Or using a manual string:

```php
$users = User::query()
    ->sort('name,-age')
    ->get();
```

## Testing

```bash
composer test
```

## Contributing

Contributions are welcome!
If you’d like to improve this package, please fork the repository and open a pull request.
Bug fixes, new features, and documentation improvements are all appreciated.

## Credits

- [Pavel Dykin](https://github.com/altrntv)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
