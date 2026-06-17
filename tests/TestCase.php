<?php

namespace Tests;

use Altrntv\EloquentFilter\EloquentFilterProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithViews;
    use LazilyRefreshDatabase;
    use WithFaker;

    /**
     * @param Application $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            EloquentFilterProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $schema = $this->app['db']->getSchemaBuilder();

        $schema->create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->unsignedSmallInteger('role');
            $table->unsignedSmallInteger('age');
            $table->timestamps();
        });

        $schema->create('posts', function (Blueprint $table): void {
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
            return 'Tests\\Models\\'
                . Str::replaceLast(
                    'Factory',
                    '',
                    Str::afterLast($factory::class, '\\')
                );
        });
    }

    /**
     * @param Application $app
     */
    protected function defineEnvironment($app): void
    {
        $driver = env('DB_DRIVER', 'sqlite');

        $app['config']->set('database.default', $driver);

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('database.connections.pgsql', [
            'driver' => 'pgsql',
            'host' => env('PG_HOST', 'pgsql'),
            'port' => 5432,
            'database' => env('PG_DATABASE', 'tracing_test'),
            'username' => env('PG_USERNAME', 'tracing'),
            'password' => env('PG_PASSWORD', 'secret'),
            'charset' => 'utf8',
            'prefix' => '',
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ]);

        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('MYSQL_HOST', 'mysql'),
            'port' => 3306,
            'database' => env('MYSQL_DATABASE', 'tracing_test'),
            'username' => env('MYSQL_USERNAME', 'tracing'),
            'password' => env('MYSQL_PASSWORD', 'secret'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]);

        $app['config']->set('eloquent-filter.namespaces.filter', 'Tests\\Filters\\');
        $app['config']->set('eloquent-filter.namespaces.sort', 'Tests\\Sorts\\');
    }
}
