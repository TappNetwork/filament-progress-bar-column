<?php

namespace Tapp\FilamentProgressBarColumn;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentProgressBarColumnServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-progress-bar-column';

    public static string $viewNamespace = 'filament-progress-bar-column';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews(static::$viewNamespace);
    }
}
