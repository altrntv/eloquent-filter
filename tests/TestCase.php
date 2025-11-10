<?php

namespace Tests;

use Altrntv\EloquentFilter\EloquentFilterProvider;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithViews;
    use LazilyRefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $manager = new Manager;

        $manager->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'username' => 'root',
            'password' => '',
        ]);

        $manager->setAsGlobal();
        $manager->bootEloquent();

        Manager::schema()->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->unsignedSmallInteger('role');
            $table->unsignedSmallInteger('age');
            $table->timestamps();
        });

        Manager::schema()->create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('tag');
            $table->timestamp('published_at');
            $table->timestamps();
        });

        Factory::guessFactoryNamesUsing(function (string $modelName): string {
            return 'Tests\\Database\\Factories\\' . Str::afterLast($modelName, '\\') . 'Factory';
        });

        Factory::guessModelNamesUsing(function (Factory $factory): string {
            return 'Tests\\Models\\' . Str::replaceLast(
                'Factory',
                '',
                Str::afterLast($factory::class, '\\')
            );
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            EloquentFilterProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('eloquent-filter.namespaces.filter', 'Tests\\Filters\\');
        $app['config']->set('eloquent-filter.namespaces.sort', 'Tests\\Sorts\\');
    }
}
