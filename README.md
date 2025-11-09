# Eloquent Filter

## Requirements

* PHP ^8.2
* Laravel ^12.0

## Installation

```
composer require altrntv/eloquent-filter
```

## Basic Usage

Extend your User model from Filterable:

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

After that, the model will have two methods, `filter()` and `filterByRequest()`.

* `filter` - accepts an array key value
* `filterByRequest` - retrieves all values from the Request by the key specified in the config

Create your first filter

```
php artisan make:eloquent-filter UserFilter
```

Add methods to process your filtering based on the names from the Request.

```php
<?php

namespace App\Filters;

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

The controller looks like this:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserIndexRequest
use Illuminate\Http\JsonResponse;

final class UserController extends Controller
{
    public function index(UserIndexRequest $request): JsonResponse
    {
        return User::query()
            ->filterByRequest()
            ->get()
            ->toResourceCollection();
    }
}
```

It is recommended to validate the filters in the request:

```php
<?php

namespace App\Http\Requests;

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

### Cast values

Currently, it supports four types of caste: `integer`, `string`, `boolean` and `array`.

To cast a value to the desired type, specify it in the `$casts` variable:

```php
<?php

namespace App\Filters;

use Altrntv\EloquentFilter\Filters\EloquentFilter;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends EloquentFilter
{
    protected array $casts = [
        'age' => 'integer',
        'roles' => 'array',
    ];

    // name filter

    public function age(int $value): Builder
    {
        return $this->builder
            ->where('age', '>=', $value);
    }

    public function roles(array $value): Builder
    {
        return $this->builder->whereIn('roles', $value);
    }
}
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'filter.name' => ['string', 'max:255'],
            'filter.age' => ['integer', 'min:0', 'max:150'], // 18
            'filter.roles' => ['string', 'regex:/^\d+(,\d+)*$/'], // 1,2,4,5
        ];
    }
}
```

The request looks like this:

## Contributing

If you'd like to contribute, please fork the repository and create a pull request. We welcome contributions of all kinds, including bug fixes, new features, and documentation improvements.

## Credits

- [Pavel Dykin](https://github.com/altrntv)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
