<?php
return [
    "envir" => [
        "ALIAS_ID" => "",
        "SECRET_KEY_BODY" => "",
        "SECRET_KEY_URL" => "",
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
            "verify_type" => NULL,
            "verified_at" => NULL,
            "password" => NULL,
            "remember_token" => NULL,
            "created_at" => "created_at",
            "updated_at" => NULL,
        ]
    ],
    "DB" => [
        "HOST" => "",
        "USER" => "",
        "PASS" => "",
        "DB_NAME" => ""
    ]
];