<?php
namespace sksso;

class SDKlrvlSSO {
    private $cryptor;
    private $config;
    private $db;
    private $strukturTable;
    private $env;

    public function __construct() {
        $this->cryptor = new Cryptor();        
        $this->config = new Configs();
    }
    
    public function setEnv($aliasID, $secretKeyBody, $secretKeyUrl)
    {
        $this->config->setEnv($aliasID, $secretKeyBody, $secretKeyUrl);
    }

    public function setDbConfig($host, $user, $pass, $db)
    {
        $this->db = $this->config->setDBConn($host, $user, $pass, $db);
    }

    public function syncDbTable($data)
    {
        $this->strukturTable = $this->config->setSyncTable($data);
    }

    private function config($key) {
        // $config = include 'config/env.php';
        $config = Configs::getEnv();
        return isset($config[$key]) ? $config[$key] : null;
    }

    public function ekstrakDataCredentials($data) {        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Request-Headers: *');
        header('Access-Control-Allow-Headers: *');

        $data_json = json_decode($data, true);
        $kode = $data_json['kode'];
        $pesan_status = $data_json['pesan']['status']; 
        $pesan_keterangan = $data_json['pesan']['keterangan']; 
        $data_token=$data_json['data']['token'];

        $hasil_data = $this->cryptor->decryptBody($data_json['data']);

        $jwt = $this->cryptor->decryptJwt($data_token);
        $hasil_data_jwt = $this->cryptor->decryptBody($jwt);
        $dataTable = Configs::getSyncTable();
        $userKey = array_search($dataTable["user_key"], $dataTable['field_table']);
        
        $hasil_data["uid_sso"] = $hasil_data["uid"];
        $hasil_data["token_sso"] = $data_token;
        $hasil_data["tiket_sso"] = $hasil_data_jwt['tiket'];
        $hasil_data["sesi_sso"] = $hasil_data_jwt['sesisso'];
        $hasil_data["sesi_app_sso"] = $hasil_data_jwt['sesiapp'];
        $hasil_data["token_created_sso"] = date("Y-m-d H:i:s", $jwt['iat']);
        $hasil_data["token_expired_sso"] = date("Y-m-d H:i:s", $jwt['exp']);
        $hasil_data["created_at"] = date("Y-m-d H:i:s", strtotime($hasil_data['created_at']));
        unset($hasil_data["uid"]);
        $dataUsers = $this->getDataUser($dataTable["nama_table"],$userKey,$hasil_data[$dataTable["user_key"]]);
        
        // $resp['kode'] = 2007;
        // $resp['pesan']['status'] = 'sukses';
        // $resp['pesan']['keterangan'] = 'Data credential diterima';
        // $resp['data'] = $dataUsers;
        // print_r(json_encode($resp));die();
        $dataInsert = [];
        $dataUpdate = [];
        if($dataUsers)
        {
            foreach ($dataTable["field_table"] as $key => $value) {
                $fieldInfo = Configs::getTableInfo($key);
                if($fieldInfo["Extra"] <> "auto_increment")
                {
                    if($value <> NULL)
                    {
                        $dataUpdate[$key] = $hasil_data[$value];
                    }
                }
            }
            $this->updateDataUser($dataTable["nama_table"], $dataUpdate, array($userKey=>$hasil_data[$dataTable["user_key"]]));
        }
        else
        {
            foreach ($dataTable["field_table"] as $key => $value) {
                $fieldInfo = Configs::getTableInfo($key);
                if($fieldInfo["Extra"] <> "auto_increment")
                {
                    if($value == NULL && $fieldInfo["Null"] == "NO")
                    {
                        if($fieldInfo["Key"] == "UNI")
                        {
                            $dataInsert[$key] = $hasil_data["nik"];
                        }
                        else
                        {
                            $dataInsert[$key] = 0;
                        }
                    }
                    if($value <> NULL)
                    {
                        $dataInsert[$key] = $hasil_data[$value];
                    }
                }
            }
            $this->insertDataUser($dataTable["nama_table"], $dataInsert);
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Request-Headers: *");
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Methods: GET");
        $resp['kode'] = 2007;
        $resp['pesan']['status'] = 'sukses';
        $resp['pesan']['keterangan'] = 'Data credential diterima';
        $resp['data'] = null;
        echo json_encode($resp);
        return;
    }

    public function cekTiket($param) {
        $data = explode(".",$param);
        $d['iv'] = $data[0];
        $d['data'] = $data[1];

        $hasil_data = $this->cryptor->decryptUrl($d);
        $tiket = $hasil_data['tiket'];
        
        $dataTable = Configs::getSyncTable();
        $dataUsers = $this->getDataUser($dataTable["nama_table"],"tiket_sso",$tiket);
        // print_r($dataUsers);die();
        $token = $dataUsers["token_sso"];

        $arr_tiket['tiket'] = $tiket;
        $post = $this->cryptor->encryptBody($arr_tiket);
        $res_data = $this->validasiTiket($post, $token);
        $kode = $res_data['kode'];
        $pesan_status = $res_data['pesan']['status']; 
        if($kode == 2009)
        {
            $dataSess = [];
            $dataSess["uid_sso"] = $dataUsers["uid_sso"];
            $dataSess["token_sso"] = $dataUsers["token_sso"];
            $dataSess["sesi_sso"] = $dataUsers["sesi_sso"];
            $dataSess["sesi_app_sso"] = $dataUsers["sesi_app_sso"];
            $dataSess["token_created_sso"] = $dataUsers["token_created_sso"];
            $dataSess["token_expired_sso"] = $dataUsers["token_expired_sso"];
		    $_SESSION["sksso_sdk_sess"]['sesi_sso_data'] = $dataSess;
            return $dataUsers;
        }
        else
        {
            $url =  $this->config('REDIRECT_LOGIN_PAGE');
            echo "<script> 
            alert ('Auth Tiket tidak Valid');
            window.location.replace('$url');
            </script>";
            return false;
        }
    }

    private function validasiTiket($post, $token)
    {
        $curl = curl_init();
		$error_msg ="";
        $post = json_encode($post);
		$as = $this->config('ALIAS_ID'); //dari sso pantek

        $url = $this->config('SSO_HOST').'api/protected/verifytiket';
		$authorization = array();
		if($token<>""){
			$authorization = array(
				"as: ".$as,
				"Authorization: Bearer ".$token,
				"Content-Type: application/json"
			);
		}

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$post,
        CURLOPT_HTTPHEADER => $authorization,
        ));

        $response = curl_exec($curl);

		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
		}

        curl_close($curl);
        return json_decode($response, true);
    }

    public function loginPage()
    {
        header('Location: '.$this->config('REDIRECT_LOGIN_PAGE'));
        exit();
    }
    
    public function logout()
    {
        $res = $this->prosesLogout(false);
        if(isset($res->kode))
        {
            if($res->kode == 4014) //Token kadaluarsa!
            {
                $data = $res->data;
                $dataToken = $data->token_baru;   
                $dataArr["iv"] = $dataToken->iv;
                $dataArr["data"] = $dataToken->data;
                $token = $this->cryptor->decryptBody($dataArr);
                $res = $this->prosesLogout($token);
                //simpan token baru ke db
                if($res->kode == 2008) //berhasil logout
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else if($res->kode == 4008) //Token tidak sah!
            {
                echo "Token tidak sah!";
                return false;
            }
            else
            {
                return true;
            }
        }
    }

    private function prosesLogout($token=false)
    {
        $dataSess = [];
        if (isset($_SESSION["sksso_sdk_sess"]['sesi_sso_data'])) {
            $dataSess = $_SESSION["sksso_sdk_sess"]['sesi_sso_data']; // Ambil dari sesi dengan namespace
        }

		$as = $this->config('ALIAS_ID'); 
        $arr_body['sesisso'] = $dataSess["sesi_sso"];
        $arr_body['sesiapp'] = $dataSess["sesi_app_sso"];
        $token = ($token?$token:$dataSess["token_sso"]);
        $post = $this->cryptor->encryptBody($arr_body);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config('SSO_HOST').'api/protected/sso/client/logout',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$post,
            CURLOPT_HTTPHEADER => array(
                'as: '.$as,
                'Authorization: Bearer '.$token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

		if (curl_errno($curl)) {
			$error_msg = curl_error($curl);
            print_r($error_msg);
            return false;
		}
        $res = json_decode($response, true);

        curl_close($curl);
        return $res;
    }

    private function getDataUser($table,$field,$val)
    {
        // Contoh query dengan prepared statement
        $stmt = $this->db->prepare('SELECT * FROM '.$table.' WHERE '.$field.' = :'.$field);
        $stmt->bindParam(':'.$field, $val);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row;
    }

    private function insertDataUser($table, $data)
    {
        // Mengambil nama field dan memformatnya
        $fields = implode(", ", array_keys($data));
        // Membuat placeholder untuk value
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO users ($fields) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);

        // Bind value ke placeholder
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        return $stmt->execute();
    }

    private function updateDataUser($table, $data, $whereKey)
    {
        // Mengolah data untuk set part
        $setPart = [];
        foreach ($data as $key => $value) {
            $setPart[] = "$key = :$key";
        }
        $setString = implode(", ", $setPart);
        
        // Mengolah data untuk where clause
        $wherePart = [];
        foreach ($whereKey as $key => $value) {
            $wherePart[] = "$key = :where_$key";
        }
        $whereString = implode(" AND ", $wherePart);
        
        $sql = "UPDATE $table SET $setString WHERE $whereString";
        
        $stmt = $this->db->prepare($sql);
        
        // Bind value ke placeholder untuk set part
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        // Bind value ke placeholder untuk where clause
        foreach ($whereKey as $key => $value) {
            $stmt->bindValue(':where_' . $key, $value);
        }
        
        return $stmt->execute();        
    }
}
