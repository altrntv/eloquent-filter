<?php

namespace Tests\Models;

use Altrntv\EloquentFilter\Traits\Filterable;
use Altrntv\EloquentFilter\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tests\Database\Factories\UserFactory;

/**
 * @method static UserFactory factory($count = null, $state = [])
 */
class User extends Model
{
    use Filterable;
    use HasFactory;
    use Sortable;

    protected $guarded = ['id'];
}
