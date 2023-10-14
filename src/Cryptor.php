<?php
namespace sksso;

class Cryptor {

    private function config($key) {
        // $config = include 'config/env.php';
        $config = Configs::getEnv();
        return isset($config[$key]) ? $config[$key] : null;
    }

    public function decryptBody($data) {
		$d_iv = $data['iv'];
		$d_data = $data['data'];
		// print_r($d_iv); die;

		$algorithm = 'aes-256-cbc';
		$iv = hex2bin($d_iv);
		$secretKey = $this->config('SECRET_KEY_BODY');
		$decryption_data = hex2bin($d_data);
		$decipher = openssl_decrypt($decryption_data, $algorithm, $secretKey, OPENSSL_RAW_DATA, $iv);
        if (false === $decipher) {
            // Jika terjadi kesalahan, tampilkan pesan error
            $error = openssl_error_string();
            die("Terjadi kesalahan: $error");
        }
		$decryptedJSON = json_decode($decipher, true);

		// print_r($decryptedJSON); die;

		return $decryptedJSON;
    }

    public function encryptBody($data) {
        $ciphering = "AES-256-CBC";
		$encryption_iv = random_bytes(16);
		$encryption_data = json_encode($data);
		$encryption_key = $this->config('SECRET_KEY_BODY');
		$encryption = openssl_encrypt($encryption_data, $ciphering, $encryption_key, OPENSSL_RAW_DATA, $encryption_iv);
		
        if (false === $encryption) {
            // Jika terjadi kesalahan, tampilkan pesan error
            $error = openssl_error_string();
            die("Terjadi kesalahan: $error");
        }
		$encryptedJSON = array(
				'iv' => bin2hex($encryption_iv),
				'data' => bin2hex($encryption)
		);
		return $encryptedJSON;
    }

    public function decryptUrl($data) {        
		$algorithm = 'aes-256-cbc';
		$d_iv = $data['iv'];
		$d_data = $data['data'];
		
		$iv = hex2bin($d_iv);
		$secretKey = $this->config('SECRET_KEY_URL');
		$decryption_data = hex2bin($d_data);
		$decipher = openssl_decrypt($decryption_data, $algorithm, $secretKey, OPENSSL_RAW_DATA, $iv);
		$decryptedJSON = json_decode($decipher, true);
        if (false === $decipher) {
            // Jika terjadi kesalahan, tampilkan pesan error
            $error = openssl_error_string();
            die("Terjadi kesalahan: $error");
        }
		return $decryptedJSON;
    }

    public function decryptJwt($data)
	{
		$arr = explode(".",$data)[1];
		$json = base64_decode($arr);
		$decoded = json_decode($json, true);
		return $decoded;
	}
}
