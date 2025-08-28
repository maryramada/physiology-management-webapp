<?php
function check_session()
{
// check if there is an active session
return isset($_SESSION['user']);
}

function aes_encrypt($value)
{
// encrypt $value
return bin2hex(openssl_encrypt($value, 'aes-256-cbc', OPENSSL_KEY, OPENSSL_RAW_DATA, OPENSSL_IV));}

function aes_decrypt($value)
{
// decrypt $value
if(strlen($value) % 2 != 0){
return false;
}
return openssl_decrypt(hex2bin($value), 'aes-256-cbc', OPENSSL_KEY, OPENSSL_RAW_DATA, OPENSSL_IV);
}