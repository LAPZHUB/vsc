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
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <header>
        <div class="logo">Sistema SAG</div>
        <nav>
            <ul>
                <li><a href="admin.php" class="active">Inicio</a></li>
                <li><a href="register_client.php">Registrar Cliente</a></li>
                <li><a href="register_user.php">Registrar Usuario</a></li>
                <li><a href="register_service.php">Registrar Servicio</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="admin-container">
            <h1>Bienvenido al Panel de Administración</h1>
            <div class="admin-options">
                <div class="option-card">
                    <h2>Registrar Cliente</h2>
                    <p>Agregue nuevos clientes al sistema.</p>
                    <a href="register_client.php" class="button">Ir</a>
                </div>
                <div class="option-card">
                    <h2>Registrar Usuario</h2>
                    <p>Agregue nuevos usuarios al sistema.</p>
                    <a href="register_user.php" class="button">Ir</a>
                </div>
                <div class="option-card">
                    <h2>Registrar Servicio</h2>
                    <p>Agregue nuevos servicios al sistema.</p>
                    <a href="register_service.php" class="button">Ir</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Sistema SAG. Todos los derechos reservados.</p>
    </footer>
</body>
</html>