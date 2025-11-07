<?php

// Configuración de la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db   = "empresa";

$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Definimos número máximo de intentos de validación
$attempts = 0;
$max_attempts = 3;

//  Ciclo para controlar los intentos
while ($attempts < $max_attempts) {

    // Verificar que se hayan enviado los datos del formulario
    if (isset($_POST['usuario'], $_POST['correo'], $_POST['clave'])) {

        // --- Sanitización de datos ---
        $user = htmlspecialchars($_POST['usuario']);
        $email  = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
        $password   = trim($_POST['clave']);

        // --- Condicional para validar los campos ---
        if (!empty($user) && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6) {
            // Encriptamos la contraseña antes de guardarla
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // --- Insert seguro con consulta preparada ---
            $stmt = $conn->prepare("INSERT INTO usuarios (usuario, correo, clave) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user, $email, $password_hash);

            if ($stmt->execute()) {
                echo "<h3>Registro exitoso</h3>";
                echo "<p>El usuario ha sido guardado de forma segura en la base de datos.</p>";
                break; // Sale del ciclo al registrar correctamente
            } else {
                // Si el correo ya existe u otro error
                echo "<h3>No se pudo registrar el usuario.</h3>";
                echo "<p>Es posible que el correo ya esté registrado.</p>";
                $attempts++;
            }

            $stmt->close();
        } else {
            echo "<h3>Datos inválidos. Intente nuevamente.</h3>";
            $attempts++;
        }

    } else {
        echo "<p>No se enviaron los datos correctamente.</p>";
        break;
    }

    // Si alcanza el máximo de intentos
    if ($attempts == $max_attempts) {
        echo "<p>Demasiados intentos fallidos. Registro bloqueado temporalmente.</p>";
    }
}

// Cerrar conexión
$conn->close();
?>
