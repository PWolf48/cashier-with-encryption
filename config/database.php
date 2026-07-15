<?php
// config/database.php

$host     = "localhost";
$username = "root";
$password = "";
$database = "kasir";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

function getEncryptionKey($conn) {
    $res = $conn->query("SELECT password FROM user WHERE role = 'owner' LIMIT 1");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        return hex2bin($row['password']); 
    }
    return false;
}

function encryptAES($data, $key) {
    $cipher = "aes-256-cbc";
    $ivLen  = openssl_cipher_iv_length($cipher);
    $iv     = openssl_random_pseudo_bytes($ivLen);
    $ciphertext = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $ciphertext);
}

function decryptAES($encryptedData, $key) {
    $cipher = "aes-256-cbc";
    $ivLen  = openssl_cipher_iv_length($cipher);
    $rawConverted = base64_decode($encryptedData, true);
    if ($rawConverted === false || strlen($rawConverted) <= $ivLen) return $encryptedData; 
    
    $iv         = substr($rawConverted, 0, $ivLen);
    $ciphertext = substr($rawConverted, $ivLen);
    return openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
}
?>