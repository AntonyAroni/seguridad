<?php
session_start();

// Verificar si el usuario tiene una sesión activa y si la verificación ya se completó
if (!isset($_SESSION['id']) || $_SESSION['verificacion_2pasos'] === true) {
    header("location: ../../login/");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <title>Verificación Telefónica</title>
    <link rel="icon" type="image/png" href="../../assets/imagenes/FavIcon.png"/>
    <link rel="stylesheet" type="text/css" href="../../assets/css/login.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
</head>
<body>
<div class="container" id="container">
    <div class="form-container sign-in-container"> <!-- Reutilizamos la misma clase para estilos consistentes -->
        <form id="phone-form">
            <h1 class="texto">Verificación Telefónica</h1>
            <div class="social-container">
                <div id="recaptcha-container" class="recaptcha"></div>
            </div>
            <input class="input" type="text" id="phone-number" placeholder="+1234567890nia" required>
            <button type="button" class="btn" id="send-code">Enviar Código</button>
        </form>

        <form id="verification-form" style="display: none;"> <!-- Mostrar cuando sea necesario -->
            <h1 class="texto">Introduce tu Código</h1>
            <input class="input" type="text" id="verification-code" placeholder="Código de verificación" required>
            <button type="button" class="btn" id="verify-code">Verificar</button>
        </form>
    </div>

    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-right">
                <h1>¡Verificación en dos pasos!</h1>
                <p>Para mantener segura tu información, verifica tu número de teléfono.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Configuración de Firebase
    const firebaseConfig = {
        apiKey: "AIzaSyBVCFB6EFWxvFHq2B1MXsTtCUG6eGfqqHM",
        authDomain: "seguridad-proyecto-acfdf.firebaseapp.com",
        projectId: "seguridad-proyecto-acfdf",
        storageBucket: "seguridad-proyecto-acfdf.firebasestorage.app",
        messagingSenderId: "133601414236",
        appId: "1:133601414236:web:df2dae2edcde16e229ea99",
        measurementId: "G-Y4D6RPQB86"
    };

    // Inicializar Firebase
    firebase.initializeApp(firebaseConfig);
    const auth = firebase.auth();

    // Configuración de reCAPTCHA
    const recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
        size: "normal",
        callback: function(response) {
            console.log("reCAPTCHA verificado.");
        },
        "expired-callback": function() {
            console.warn("reCAPTCHA expirado.");
        }
    });
    recaptchaVerifier.render();

    // Enviar código de verificación
    document.getElementById('send-code').addEventListener('click', function () {
        const phoneNumber = document.getElementById('phone-number').value;

        firebase.auth().signInWithPhoneNumber(phoneNumber, recaptchaVerifier)
            .then((confirmationResult) => {
                // Código enviado correctamente
                window.confirmationResult = confirmationResult;
                alert("Código enviado.");
                document.getElementById('phone-form').style.display = 'none';
                document.getElementById('verification-form').style.display = 'block';
            })
            .catch((error) => {
                console.error("Error al enviar el código:", error.message);
                alert("Error al enviar el código: " + error.message);
            });
    });

    // Verificar el código ingresado
    document.getElementById('verify-code').addEventListener('click', function () {
        const code = document.getElementById('verification-code').value;

        window.confirmationResult.confirm(code)
            .then((result) => {
                // Código verificado correctamente
                const user = result.user;
                console.log("Usuario autenticado:", user);

                // Redirigir al área principal
                window.location.href = '../../templates/inicio.php';
            })
            .catch((error) => {
                console.error("Error al verificar el código:", error.message);
                alert("Error al verificar el código: " + error.message);
            });
    });
</script>
</body>
</html>