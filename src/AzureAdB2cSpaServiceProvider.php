<?php

namespace WikaGroup\AzureAdB2cSpa;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AzureAdB2cSpaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('azureb2cspa')
            ->hasConfigFile(['azureadb2c'])
            ->hasViews('azureb2cspa')
            ->hasMigration('add_oauth_id_to_users_table')
            ->hasRoute('api')
            ->hasAssets();

        Blade::directive('azureB2cSpaScripts', function () {
            return '<script src="' . url('vendor/azureb2cspa/js/msal-browser.min.js') . '"></script>' .
                '<script src="' . url('vendor/azureb2cspa/js/azureadb2c-spa.js') . '"></script>' .
                implode('', file(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'resources', 'views', 'login.blade.php'])));
        });
    }
}
