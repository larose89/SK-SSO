<?php
// Mendeteksi Laravel
if (file_exists(getcwd() . '/artisan')) {
    // Ini adalah Laravel
    try
    {
        // Tambahkan provider ke app.php
        $configApp = file_get_contents(getcwd() . '/config/app.php');
        if (!strpos($configApp, 'SDKlrvlSSOServiceProvider::class')) {
            $providerToAdd = "\n        App\\Providers\\SDKlrvlSSOServiceProvider::class,";
            $configApp = str_replace("'providers' => [", "'providers' => [" . $providerToAdd, $configApp);
            file_put_contents(getcwd() . '/config/app.php', $configApp);
        }
    } catch (Exception $e) {
        // Anda bisa memutuskan untuk menampilkan pesan kesalahan atau log kesalahan.
        echo "Terjadi kesalahan saat menulis ke config/app.php: " . $e->getMessage();
    }
    // Salin sdksksso.php ke folder config
    if (!file_exists(getcwd() . '/app/Providers/SDKlrvlSSOServiceProvider.php')) {
        copy('src/utils/source/laravel/Providers/SDKlrvlSSOServiceProvider.php', getcwd() . '/app/Providers/SDKlrvlSSOServiceProvider.php');
    }

    // Salin sdksksso.php ke folder config
    if (!file_exists(getcwd() . '/config/sdksksso.php')) {
        copy('src/utils/config/sdksksso.php', getcwd() . '/config/sdksksso.php');
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
