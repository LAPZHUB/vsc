<?php
session_start();
include 'db.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para obtener el usuario con el nombre de usuario dado
    $stmt = $conn->prepare("SELECT id, username, password, role, activo FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar si el usuario está activo
        if ($user['activo'] == 1) {
            // Verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // La contraseña es correcta, iniciar la sesión
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header('Location: admin.php');
                exit();
            } else {
                // La contraseña es incorrecta
                echo "Usuario o contraseña incorrectos.";
            }
        } else {
            // El usuario no está activo
            echo "Usuario no activo. Contacte al administrador.";
        }
    } else {
        // No se encontró el usuario o hay más de uno
        echo "Usuario o contraseña incorrectos.";
    }

    $stmt->close();
}

$conn->close();
?>
