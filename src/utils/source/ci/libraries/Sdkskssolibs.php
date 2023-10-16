<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use sksso\SDKlrvlSSO;

class Sdkskssolibs {

    protected $ci;

    public function __construct()
    {
        // Dapatkan instance CI
        $this->ci =& get_instance();

        // Konfigurasi awal SDKlrvlSSO
        $this->sdk = new SDKlrvlSSO();
        $this->sdk->setDbConfig('host', 'user', 'pass', 'db');
    }

    public function getSdk()
    {
        return $this->sdk;
    }
}
