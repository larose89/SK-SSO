[![PHP Composer](https://github.com/larose89/SK-SSO/actions/workflows/php.yml/badge.svg)](https://github.com/larose89/SK-SSO/actions/workflows/php.yml)
# Dokumentasi SDK SSO Smartkampung untuk Web PHP dengan DB MySQL

## 1.	Deskripsi
   SDK SSO ini memungkinkan integrasi yang mudah dengan layanan SSO (Single Sign-On) Smartkampung. Dengan SDK ini, aplikasi dapat dengan mudah memvalidasi dan mengelola autentikasi pengguna melalui SSO Smartkampung.

## 2. Alur Autentikasi Login SSO
 ![auth_sso](https://github.com/larose89/SK-SSO/assets/6192277/639f54e1-1a67-4c3a-bfa5-59ee8fce6735)

### Keterangan:
**A. Redirect Login**<br />
Redirect ke halaman login SSO.<br /><br />
**B. Ekstrak Data**<br />
Mengekstrak data kredensial dari server SSO yang sebelumnya data sudah di enkripsi.<br /><br />
**C. Get Tiket**<br />
Menerima tiket login berupa data enkripsi dari server SSO dengan menggunakan method GET Request.<br /><br />
**D. Validasi Tiket**<br />
Mevalidasi tiket login yang sebelumnya sudah diterima dari server SSO.<br /><br />
**E. Login Auth By ID**<br />
Jika tiket valid, maka dilanjut ke proses auth login yang dari bawaan web framework masing-masing tetapi dengan menggunakan metode login by ID user tanpa menggunakan password, dikarenakan bagian login ini tanpa menggunakan password, demi keamanan website, maka daripada itu untuk fungsi auth login ini jadikanlah dalam satu method di controller untuk proses pada poin C,D,E diatas.<br /><br />
**F. User Logout**<br />
Mengirim data session user ke server SSO untuk dilakukannya proses logout di server<br />

## 3. Instalasi SDK
Untuk instalasi SDK ini, gunakanlah composer.
Buka command promp/terminal, masuk ke direktori utama projek anda, dengan menjalankan perintah berikut:
```
composer require sksso/sdklrvlsso
```

## 4. Konfigurasi
### A. Import SDK
Isikan kode dibawah ini untuk mengimport SDK di bagian use statements pada class controller.
use sksso\SDKlrvlSSO;

### B. Set Environment
<table>
  <tr>
    <td><b>Parameter</b></td>
    <td><b>Value</b></td>
  </tr>
  <tr>
    <td>ALIAS_ID</td>
    <td><i>Untuk mendapatkannya hubungi admin SSO</i></td>
  </tr>
  <tr>
    <td>SECRET_KEY_BODY</td>
    <td><i>Untuk mendapatkannya hubungi admin SSO</i></td>
  </tr>
  <tr>
    <td>SECRET_KEY_URL</td>
    <td><i>Untuk mendapatkannya hubungi admin SSO</i></td>
  </tr>
</table>

Masukkan value dari parameter environment diatas kedalam fungsi **_setEnv()_** yang anda letakkan di controller, sebelumnya anda harus menginisialisasikan kode ini **_$this->sdkSSO = new SDKlrvlSSO()_** di bagian atas kode, misalkan dimasukkan kedalam method konstruktor, seperti berikut

```HTML+PHP
private $sdkSSO;

public function __construct() {
    //inisialisasi konfigurasi SSO
    $this->sdkSSO = new SDKlrvlSSO();
    $this->sdkSSO->setEnv([ALIAS_ID], [SECRET_KEY_BODY], [SECRET_KEY_URL]);
    ....
```

  

### C. Login Page
Untuk mengarahkan ke halaman login SSO, gunakan kode berikut
```HTML+PHP
$this->sdkSSO->loginPage();
```

### D. Set Konfigurasi Database
Anda dapat meng set koneksi database MySQL dengan kode berikut
```HTML+PHP
$this->sdkSSO->setDbConfig([HOST], [DB_USER], [DB_PASS], [DB_NAME]);
```

### E. Sinkronisasi Tabel Database
Sebelum melakukan sinkronisasi tabel database dengan parameter data array yang dikirim oleh server SSO, anda harus mengetahui terlebih dahulu nama-nama parameter yang akan disinkronkan dengan nama-nama kolom pada tabel user/pengguna yang sudah tersedia di database anda, berikut parameter dari server SSO
<table>
  <tr>
    <td><b>Nama Parameter</b></td>
    <td><b>Type</b></td>
  </tr>
  <tr>
    <td>nama</td>
    <td>VARCHAR(50)</td>
  </tr>
  <tr>
    <td>phone</td>
    <td>VARCHAR(20)</td>
  </tr>
  <tr>
    <td>email</td>
    <td>VARCHAR(100)</td>
  </tr>
  <tr>
    <td>nik</td>
    <td>VARCHAR(20)</td>
  </tr>
  <tr>
    <td>created_at</td>
    <td>DATETIME</td>
  </tr>
</table>

Dalam kasus ini misalkan nama-nama kolom di tabel user yang ada di database anda seperti berikut ini
<table>
  <tr>
    <td><b>Nama Kolom</b></td>
  </tr>
  <tr>
    <td>id</td>
  </tr>
  <tr>
    <td>username</td>
  </tr>
  <tr>
    <td>password</td>
  </tr>
  <tr>
    <td>nama_lengkap</td>
  </tr>
  <tr>
    <td>alamat</td>
  </tr>
  <tr>
    <td>email</td>
  </tr>
  <tr>
    <td>nik</td>
  </tr>
  <tr>
    <td>created_by</td>
  </tr>
  <tr>
    <td>created_date</td>
  </tr>
</table>
Untuk nama-nama kolom tabel wajib ditulis/dimasukkan semua yang ada di tabel database untuk disinkronkan. Sinkronisasi/selaraskan nama-nama kolom tabel diatas dengan nama-nama parameter array dari server SSO, seperti contoh berikut ini
<table>
  <tr>
    <td><b>Nama Kolom Tabel</b></td>
    <td><b>Nama Parameter SSO</b></td>
  </tr>
  <tr>
    <td>id</td>
    <td>NULL</td>
  </tr>
  <tr>
    <td>username</td>
    <td>NULL</td>
  </tr>
  <tr>
    <td>password</td>
    <td>NULL</td>
  </tr>
  <tr>
    <td>nama_lengkap</td>
    <td>nama</td>
  </tr>
  <tr>
    <td>alamat</td>
    <td>NULL</td>
  </tr>
  <tr>
    <td>email</td>
    <td>email</td>
  </tr>
  <tr>
    <td>nik</td>
    <td>nik</td>
  </tr>
  <tr>
    <td>created_by</td>
    <td>NULL</td>
  </tr>
  <tr>
    <td>created_date</td>
    <td>created_at</td>
  </tr>
</table>

Jika tabel sudah disinkronisasikan maka rubah data tabel diatas ke bentuk array dengan key 
- **nama_table**_(isikan nilai dengan nama tabel dari tabel yang digunakan untuk disinkronkan)_, 
- **user_key**_(diisikan dengan salah satu nama parameter berikut: **nik,email,phone**)_, dan
- **field_table**_(isikan dengan data array dari data tabel sinkronisasi data diatas)_

berikut contoh data arraynya
```HTML+PHP
$dataTable = 
        [
            "nama_table" => "users",
            "user_key" => "nik",
            "field_table" => [
                "id" => NULL,
                "username" => NULL,
                "password" => NULL,
                "nama_lengkap" => "name",
                "alamat" => NULL,
                "email" => "email",
                "nik" => "nik",
                "created_by" => NULL,
                "created_date" => "created_at"
            ]
        ];
```
Masukkan data array diatas kedalam fungsi berikut
```HTML+PHP
$this->sdkSSO->syncDbTable($dataTable);
```

Berikut kutipan gabungan kode script dari tahapan-tahapan diatas
```HTML+PHP
use sksso\SDKlrvlSSO;

class NamaController extends Controller
{
private $sdkSSO;

public function __construct() {
    //inisialisasi konfigurasi SSO
    $this->sdkSSO = new SDKlrvlSSO();
          $dataTable = 
         [
            "nama_table" => "users",
            "user_key" => "nik",
            "field_table" => [
                "id" => NULL,
                "username" => NULL,
                "password" => NULL,
                "nama_lengkap" => "name",
                "alamat" => NULL,
                "email" => "email",
                "nik" => "nik",
                "created_by" => NULL,
                "created_date" => "created_at"
            ]
    ];
    $this->sdkSSO->setEnv([ALIAS_ID], [SECRET_KEY_BODY], [SECRET_KEY_URL]);
    $this->sdkSSO->setDbConfig([HOST], [DB_USER], [DB_PASS], [DB_NAME]);
    $this->sdkSSO->syncDbTable($dataTable);
}
```

## 5. Penggunaan SDK
### A. Ekstrak Data Kredensial
Buatlah method di controller untuk menerima dan meng ekstrak data **_POST** kredensial yang dikirim dari server SSO. Berikut contoh kodenya
```HTML+PHP
    public function ekstrakData(){
        $data = file_get_contents('php://input');
        
        return $this->sdkSSO->ekstrakDataCredentials($data);
    }
```
Beritahukan ke admin SSO, url endpoint dari method diatas, supaya dapat diakses dari server SSO

### B. Cek Validasi Tiket
Buatlah method di controller untuk menerima data **_GET** tiket yang dikirim dari server SSO. Berikut contoh kodenya
```HTML+PHP
    public function login(Request $request, $param){
        $dataUsers = $this->sdkSSO->cekTiket($param);
        if($dataUsers) //output data kredensial pengguna format array 
        {
            Auth::loginUsingId($dataUsers['id']); //auth login by ID
            return redirect()->route('home'); //kondisi setelah user dapat login
        }
    }
```
Beritahukan ke admin SSO, url endpoint dari method diatas, supaya dapat diakses dari server SSO.<br/><br/>
Dari kode script diatas untuk baris ini _**Auth::loginUsingId($dataUsers['id']);**_ ini merupakan fungsi dari Laravel(untuk framework lain bisa menyesuaikan), metode ini berfungsi untuk melakukan autentikasi login berdasarkan ID pengguna tanpa memerlukan password.

### C. Logout
Berikut merupakan kode script untuk perintah logout user
```HTML+PHP
 $this->sdkSSO->logout();
```
