<?php
return [
    "envir" => [
        "ALIAS_ID" => "285a029f-431f-4b23-afdc-c4ab79f13b95",
        "SECRET_KEY_BODY" => "1oQKL4q3UcSHIvGmddLFu5dkCfPz1sqv",
        "SECRET_KEY_URL" => "Bv78XRzSMF3rxRC6dgpVfYW8RWbvxg3r",
    ],
    "tableSync" => [
        "nama_table" => "users",
        "user_key" => "nik",
        "field_table" => [
            "id" => NULL,
            "name" => "name",
            "username" => NULL,
            "phone" => "phone",
            "email" => "email",
            "nik" => "nik",
            "appid" => NULL,
            "sesisso" => NULL,
            "sesiapp" => NULL,
            "iat" => NULL,
            "exp" => NULL,
            "token" => NULL,
            "tiket" => NULL,
            "verify_type" => NULL,
            "verified_at" => NULL,
            "password" => NULL,
            "remember_token" => NULL,
            "created_at" => "created_at",
            "updated_at" => NULL,
            "last_login_time" => NULL,
            "last_login_ip" => NULL
        ]
    ],
    "DB" => [
        "HOST" => env('DB_HOST', '127.0.0.1'),
        "USER" => env('DB_USERNAME', 'forge'),
        "PASS" => env('DB_PASSWORD', ''),
        "DB_NAME" => env('DB_DATABASE', 'forge')
    ]
];