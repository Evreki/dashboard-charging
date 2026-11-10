<?php
// config.php

$serverName = "10.19.16.21";
$connectionInfo = array(
    "Database" => "prod_control",
    "UID" => "sql_pre",
    "PWD" => "", // Masukkan password di .env atau konfigurasi server lokal
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(json_encode([
        "error" => "Koneksi Database Gagal!", 
        "details" => sqlsrv_errors()
    ]));
}
?>
