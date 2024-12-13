<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Superusuario</title>
    <link rel="stylesheet" href="styles.css"> <!-- Estilos generales -->
    <link rel="stylesheet" href="admin.css"> <!-- Estilos específicos para administración -->
</head>
<body>
    <header>
        <div class="header-logo">
            <img src="LAPZ_LOGO.png" alt="Logotipo" class="logo">
        </div>
        <h1>Administración de Superusuario</h1>
        <nav>
            <ul class="menu">
                <li><a href="#">Registrar</a>
                    <ul class="submenu">
                        <li><a href="register_user.php">Usuario</a></li>
                        <li><a href="register_client.php">Cliente</a></li>
                        <li><a href="register_service.php">Servicio</a></li>
                    </ul>
                </li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <div class="admin-container">
        <h2>Bienvenido, <?php echo $_SESSION['username']; ?></h2>
        <p>Contenido adicional para la página de administración.</p>
    </div>
    <script src="script.js"></script> <!-- Asegúrate de tener este archivo JavaScript -->
</body>
</html>