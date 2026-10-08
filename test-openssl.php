<?php

$key = openssl_pkey_new([
    'curve_name' => 'prime256v1',
    'private_key_type' => OPENSSL_KEYTYPE_EC,
]);

var_dump($key);

while ($error = openssl_error_string()) {
    echo $error . PHP_EOL;
}