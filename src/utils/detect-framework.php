<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Mendeteksi Laravel
if (file_exists(getcwd() . '/artisan')) {
    // Ini adalah Laravel
    // Tambahkan provider ke app.php
    try
    {
        $configApp = file_get_contents(getcwd() . '/config/app.php');
        if (!strpos($configApp, 'SDKlrvlSSOServiceProvider::class')) {
            $providerToAdd = "\n        App\\Providers\\SDKlrvlSSOServiceProvider::class,";
            $configApp = str_replace("'providers' => [", "'providers' => [" . $providerToAdd, $configApp);
            file_put_contents(getcwd() . '/config/app.php', $configApp);
        }
        
        // Salin sdksksso.php ke folder config
        if (!file_exists(getcwd() . '/app/Providers/SDKlrvlSSOServiceProvider.php')) {
            copy(__DIR__ . '/source/laravel/Providers/SDKlrvlSSOServiceProvider.php', getcwd() . '/app/Providers/SDKlrvlSSOServiceProvider.php');
        }

        // Salin sdksksso.php ke folder config
        if (!file_exists(getcwd() . '/config/sdksksso.php')) {
            copy(__DIR__ . '/config/sdksksso.php', getcwd() . '/config/sdksksso.php');
        }
    } catch (Exception $e) {
        // Anda bisa memutuskan untuk menampilkan pesan kesalahan atau log kesalahan.
        echo "Terjadi kesalahan saat menulis ke config/app.php: " . $e->getMessage();
    }
}
// Mendeteksi CodeIgniter
if (is_dir(getcwd() .'/application') && is_dir(getcwd() .'/system') && file_exists(getcwd() .'/index.php')) {
    // // Mencari string khas CodeIgniter dalam file index.php
    // $content = file_get_contents('index.php');
    // if (strpos($content, '$application_folder = \'application\';') !== false) {
    //     return 'CodeIgniter';
    // }
}
