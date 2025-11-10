<?php

arch()
    ->expect('Altrntv\\EloquentFilter')
    ->not->toUse(['die', 'dd', 'dump']);
