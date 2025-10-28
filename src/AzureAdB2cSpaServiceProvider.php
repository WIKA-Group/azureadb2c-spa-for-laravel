<?php

namespace WikaGroup\AzureAdB2cSpa;

class AzureAdB2cSpaServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider
{
    public function configurePackage(\Spatie\LaravelPackageTools\Package $package): void
    {
        $package
            ->name('azureb2cspa')
            ->hasConfigFile(['azureadb2c'])
            ->hasViews('azureadb2c')
            ->hasMigration('add_oauth_id_to_users_table')
            ->hasAssets();
    }

    public function packageBooted()
    {
        \Livewire\Livewire::component('azureB2cSpaScripts', Components\Scripts::class);
    }
}
