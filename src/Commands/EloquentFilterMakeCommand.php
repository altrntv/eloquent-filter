<?php

namespace Altrntv\EloquentFilter\Commands;

use Altrntv\EloquentFilter\Config\ConfigHelper;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:eloquent-filter', aliases: [
    'eloquent:filter',
])]
class EloquentFilterMakeCommand extends GeneratorCommand
{
    protected $name = 'make:eloquent-filter';

    protected $description = 'Create a new Eloquent Filter';

    protected $type = 'Eloquent Filter';

    /** @var string[] */
    protected $aliases = [
        'eloquent:filter',
    ];

    protected function getNameInput(): string
    {
        $name = trim($this->argument('name'));

        if (Str::endsWith($name, '.php')) {
            $name = Str::substr($name, 0, -4);
        }

        if (Str::doesntEndWith($name, 'Filter')) {
            $name .= 'Filter';
        }

        return Str::pascal($name);
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return trim(ConfigHelper::namespace(), '\\');
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => [
                'What should the eloquent filter be named?',
                'E.g. PostFilter',
            ],
        ];
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/filter.stub';
    }
}
