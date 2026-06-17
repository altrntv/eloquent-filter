<?php

arch()
    ->expect('Altrntv\\EloquentFilter')
    ->not->toUse(['die', 'dd', 'dump', 'var_dump']);

arch()
    ->expect('Altrntv\\EloquentFilter\\Filters\\EloquentFilter')
    ->toBeAbstract();

arch()
    ->expect('Altrntv\\EloquentFilter\\Sorts\\EloquentSort')
    ->toBeAbstract();

arch()
    ->expect('Altrntv\\EloquentFilter\\Contracts')
    ->toBeInterfaces();
