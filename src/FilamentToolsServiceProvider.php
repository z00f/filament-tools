<?php

namespace z00f\FilamentTools;

use Filament\PluginServiceProvider;

class FilamentToolsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-tools';

    protected array $pages = [
        Tools::class,
    ];
}
