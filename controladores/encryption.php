<?php
// Definir método de encriptación y claves
define('ENCRYPT_METHOD', 'AES-256-CBC'); // Método de encriptación
define('SECRET_KEY', 'clave_secreta');  // Clave secreta
define('SECRET_IV', 'vector_inicializacion'); // Vector de inicialización (IV)

// Función para encriptar texto
function encrypt($data) {
    $key = hash('sha256', SECRET_KEY); // Deriva la clave
    $iv = substr(hash('sha256', SECRET_IV), 0, 16); // Deriva el IV
    return openssl_encrypt($data, ENCRYPT_METHOD, $key, 0, $iv);
}

// Función para desencriptar texto
function decrypt($data) {
    $key = hash('sha256', SECRET_KEY); // Deriva la clave
    $iv = substr(hash('sha256', SECRET_IV), 0, 16); // Deriva el IV
    return openssl_decrypt($data, ENCRYPT_METHOD, $key, 0, $iv);
}
?>