<?php

use Illuminate\Http\Request;
use Tests\Models\User;

describe('scopeFilterByRequest', function () {
    beforeEach(function () {
        User::factory(5)->state(['role' => 1])->create();
        User::factory(3)->state(['role' => 2])->create();
    });

    it('filters records using filter parameters from the request', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'filter' => ['role' => '1'],
        ]));

        expect(User::filterByRequest()->count())->toBe(5);
    });

    it('returns all records when the filter key is absent in the request', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET'));

        expect(User::filterByRequest()->count())->toBe(8);
    });

    it('returns all records when the filter value is an empty array', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'filter' => [],
        ]));

        expect(User::filterByRequest()->count())->toBe(8);
    });

    it('filters multiple roles from request', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'filter' => ['role' => '1,2'],
        ]));

        expect(User::filterByRequest()->count())->toBe(8);
    });

    it('ignores unknown filter keys from the request', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'filter' => ['nonExistent' => 'value'],
        ]));

        expect(User::filterByRequest()->count())->toBe(8);
    });

    it('ignores null filter values from the request', function () {
        $this->app->instance(Request::class, Request::create('/', 'GET', [
            'filter' => ['role' => null],
        ]));

        expect(User::filterByRequest()->count())->toBe(8);
    });
});
