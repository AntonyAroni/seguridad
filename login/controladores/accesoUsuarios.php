<?php
// Inicio de sesión
SESSION_START();
include("../../conexion/conexion.php");
$conn = conectar();

$correo = $_POST["login_correo"];
$pass = $_POST["login_contrasenia"];
$tiempoBloqueo = 60; // Tiempo de bloqueo en segundos
$tiempoReinicioIntentos = 600; // Tiempo para reiniciar intentos en segundos (10 minutos)

if (isset($correo)) {
    // Verificar si el correo existe en la base de datos
    $consulta = "SELECT * FROM usuarios WHERE correoUsuario='$correo'";
    $resultados = mysqli_query($conn, $consulta) or die(mysqli_error($conn));
    $usuario = mysqli_fetch_assoc($resultados);

    if ($usuario) {
        $intentosFallidos = $usuario['intentosFallidos'];
        $bloqueoHasta = $usuario['bloqueoHasta'];

        // Verificar si el usuario está bloqueado
        if ($bloqueoHasta && strtotime($bloqueoHasta) > time()) {
            $tiempoRestante = strtotime($bloqueoHasta) - time();
            echo "<script>alert('Tu cuenta está bloqueada. Inténtalo de nuevo en $tiempoRestante segundos.'); window.location.href='../../login/';</script>";
            exit();
        }

        // Reiniciar intentos si han pasado más de 10 minutos desde el último intento
        if ($bloqueoHasta && strtotime($bloqueoHasta) <= time()) {
            $intentosFallidos = 0;
            $bloqueoHasta = null;
            $actualizar = "UPDATE usuarios SET intentosFallidos=0, bloqueoHasta=NULL WHERE correoUsuario='$correo'";
            mysqli_query($conn, $actualizar);
        }

        // Verificar la contraseña
        if (password_verify($pass, $usuario['claveUsuario'])) {
            // Inicio de sesión exitoso: restablecer intentos fallidos
            $actualizar = "UPDATE usuarios SET intentosFallidos=0, bloqueoHasta=NULL WHERE correoUsuario='$correo'";
            mysqli_query($conn, $actualizar);

            $_SESSION["id"] = $usuario['idUsuario'];
            $_SESSION["nombreUsuario"] = $usuario['username'];
            $_SESSION["permisos"] = $usuario['idTipo'];
            $_SESSION["fotoU"] = $usuario['fotoUsuario'];
            $_SESSION["verificacion_2pasos"] = false;

            echo "<script>alert('Inicio de sesión exitoso.'); window.location.href='../../login/controladores/verificacionTelefonicaFirebase.php';</script>";
            exit();
        } else {
            // Contraseña incorrecta: incrementar intentos fallidos
            $intentosFallidos++;
            if ($intentosFallidos >= 4) {
                // Bloquear usuario
                $bloqueoHasta = date("Y-m-d H:i:s", time() + $tiempoBloqueo);
                $actualizar = "UPDATE usuarios SET intentosFallidos=$intentosFallidos, bloqueoHasta='$bloqueoHasta' WHERE correoUsuario='$correo'";
                mysqli_query($conn, $actualizar);

                echo "<script>alert('Demasiados intentos fallidos. Tu cuenta está bloqueada durante $tiempoBloqueo segundos.'); window.location.href='../../login/';</script>";
                exit();
            } else {
                // Actualizar intentos fallidos en la base de datos
                $actualizar = "UPDATE usuarios SET intentosFallidos=$intentosFallidos WHERE correoUsuario='$correo'";
                mysqli_query($conn, $actualizar);

                echo "<script>alert('Credenciales incorrectas. Intento $intentosFallidos de 4.'); window.location.href='../../login/';</script>";
                exit();
            }
        }
    } else {
        // Si el usuario no existe
        echo "<script>alert('Credenciales incorrectas.'); window.location.href='../../login/';</script>";
        exit();
    }
} else {
    echo "<script>alert('Por favor, ingrese sus datos.'); window.location.href='../../login/';</script>";
}