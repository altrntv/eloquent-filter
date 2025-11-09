<?php

namespace Altrntv\EloquentFilter\Commands;

use Altrntv\EloquentFilter\Config\ConfigHelper;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:eloquent-sort', aliases: [
    'eloquent:sort',
])]
class EloquentSortMakeCommand extends GeneratorCommand
{
    protected $name = 'make:eloquent-sort';

    protected $description = 'Create a new Eloquent Sort';

    protected $type = 'Eloquent Sort';

    /** @var string[] */
    protected $aliases = [
        'eloquent:sort',
    ];

    protected function getNameInput(): string
    {
        $name = trim($this->argument('name'));

        if (Str::endsWith($name, '.php')) {
            $name = Str::substr($name, 0, -4);
        }

        if (Str::doesntEndWith($name, 'Sort')) {
            $name .= 'Sort';
        }

        return Str::pascal($name);
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return trim(ConfigHelper::sortNamespace(), '\\');
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => [
                'What should the eloquent sort be named?',
                'E.g. PostSort',
            ],
        ];
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/sort.stub';
    }
}
