<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use sksso\SDKlrvlSSO;

class SDKlrvlSSOServiceProvider extends ServiceProvider {
    public function register() {
        $this->app->singleton(SDKlrvlSSO::class, function ($app) {
            $sdk = new SDKlrvlSSO();
            
            $config = $app['config']['sdksksso'];
            $sdk->setEnv($config['envir']['ALIAS_ID'], $config['envir']['SECRET_KEY_BODY'], $config['envir']['SECRET_KEY_URL']);
            $sdk->setDbConfig($config['DB']['HOST'], $config['DB']['USER'], $config['DB']['PASS'], $config['DB']['DB_NAME']);
            $sdk->syncDbTable($config['tableSync']);
            return $sdk;
        });
    }

    public function boot() {
        // Anda bisa menambahkan penerbitan aset, migrasi, dll di sini jika diperlukan
    }
}
