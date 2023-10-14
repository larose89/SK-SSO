<?php
namespace sksso;

use PDO;

class Configs {
	private static $env;
	private static $syncTable;
	private static $tableInfo;
	private $pdo;
    private const SESSION_NAMESPACE = 'sksso_sdk_sess';

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION[self::SESSION_NAMESPACE])) {
            $_SESSION[self::SESSION_NAMESPACE] = [];
        }
    }

    public function setDBConn($host, $user, $pass, $db) {
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
		try {
			$this->pdo = new PDO($dsn, $user, $pass, $options);
			return $this->pdo;
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
    }

    public function setSyncTable($data) {
		// print_r(array_values($data["field_table"]));die();
		$paramSSO = [
			"uid_sso" => "VARCHAR(50)",
			"sesi_sso" => "VARCHAR(255)",
			"sesi_app_sso" => "VARCHAR(255)",
			"tiket_sso" => "VARCHAR(255)",
			"token_sso" => "TEXT",
			"token_created_sso" => "DATETIME",
			"token_expired_sso" => "DATETIME",
		];
		$newField = [];
		$stmt = $this->pdo->prepare("DESCRIBE ".$data["nama_table"]);
		$stmt->execute();
		$table_fields = $stmt->fetchAll(PDO::FETCH_COLUMN);
	
		foreach ($paramSSO as $key => $value) {
			if(!in_array($key,array_values($data["field_table"])))
			{
				if(!in_array($key,array_values($table_fields)))
				{
					$newField[] = "ADD $key $value";
				}
				$data["field_table"][$key] = $key;
			}
		}
		
		$sql = "ALTER TABLE ".$data["nama_table"]." ".implode(", ", $newField);
        $this->pdo->exec($sql);
		$_SESSION[self::SESSION_NAMESPACE]['sync_table_data'] = $data;
		self::$syncTable = $data;

		$sql = "DESCRIBE ".$data["nama_table"];
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$tbInfo = [];
		foreach ($columns as $row) {
			$fieldName = $row['Field'];
			unset($row['Field']);
			$tbInfo[$fieldName] = $row;
		}
		$_SESSION[self::SESSION_NAMESPACE]['table_info_data'] = $tbInfo;
		self::$tableInfo = $tbInfo;
		// +----------+--------------+------+-----+---------+----------------+
		// | Field    | Type         | Null | Key | Default | Extra          |
		// +----------+--------------+------+-----+---------+----------------+
		// | id       | int          | NO   | PRI | NULL    | auto_increment |
		// | email    | varchar(255) | NO   | UNI | NULL    |                |
		// | password | varchar(255) | YES  |     | NULL    |                |
		// +----------+--------------+------+-----+---------+----------------+

		return;
    }

	public static function getSyncTable() 
	{
        if (!self::$syncTable && isset($_SESSION[self::SESSION_NAMESPACE]['sync_table_data'])) {
            self::$syncTable = $_SESSION[self::SESSION_NAMESPACE]['sync_table_data']; // Ambil dari sesi dengan namespace
        }
		return self::$syncTable;
	}

	public static function getTableInfo($key) 
	{
        if (!self::$tableInfo && isset($_SESSION[self::SESSION_NAMESPACE]['table_info_data'])) {
            self::$tableInfo = $_SESSION[self::SESSION_NAMESPACE]['table_info_data']; // Ambil dari sesi dengan namespace
        }
		return self::$tableInfo[$key];
	}	

    public function setEnv($aliasID, $secretKeyBody, $secretKeyUrl) {
		// https://sso.banyuwangikab.go.id/
		$data = [
			'SSO_HOST' => 'https://sso.banyuwangikab.go.id/',
			'REDIRECT_LOGIN_PAGE' => 'https://sso.banyuwangikab.go.id/user/login/0?as='.$aliasID,
			'SECRET_KEY_BODY' => $secretKeyBody,
			'SECRET_KEY_URL' => $secretKeyUrl,
			'ALIAS_ID' => $aliasID
		];
        $_SESSION[self::SESSION_NAMESPACE]['env_data'] = $data; // Menyimpan data ke sesi dengan namespace
		self::$env = $data;
		return $data;
    }

	public static function getEnv() 
	{
        if (!self::$env && isset($_SESSION[self::SESSION_NAMESPACE]['env_data'])) {
            self::$env = $_SESSION[self::SESSION_NAMESPACE]['env_data']; // Ambil dari sesi dengan namespace
        }
		return self::$env;
	}	
}
