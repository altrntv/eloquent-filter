<?php

use Illuminate\Http\Request;
use Tests\Models\User;

describe('scopeSortByRequest', function () {
    beforeEach(function () {
        User::factory()->state(['name' => 'Alice', 'age' => 30])->create();
        User::factory()->state(['name' => 'Bob',   'age' => 20])->create();
        User::factory()->state(['name' => 'Charlie', 'age' => 25])->create();
    });

    it('sorts ascending by the sort_by key from the request', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'sort_by' => 'name',
        ]));

        expect(User::sortByRequest()->first()->name)->toBe('Alice');
    });

    it('sorts descending when the column is prefixed with a dash', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'sort_by' => '-name',
        ]));

        expect(User::sortByRequest()->first()->name)->toBe('Charlie');
    });

    it('returns all records without sorting when sort_by is absent', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET'));

        expect(User::sortByRequest()->count())->toBe(3);
    });

    it('returns all records without error when sort_by is an empty string', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'sort_by' => '',
        ]));

        expect(User::sortByRequest()->count())->toBe(3);
    });

    it('sorts by age ascending from request', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'sort_by' => 'age',
        ]));

        expect(User::sortByRequest()->first()->age)->toBe(20);
    });

    it('sorts by age descending from request', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'sort_by' => '-age',
        ]));

        expect(User::sortByRequest()->first()->age)->toBe(30);
    });
});
