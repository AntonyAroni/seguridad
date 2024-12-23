<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    SESSION_START();
    //Validar si se ingresa sin login
    if (!$_SESSION) {
        header("location: ../login/");
    }

    //Conexion a la base de datos
    include("../conexion/conexion.php");
    $conn=conectar();
?>