<?php
include 'db.php'; // Archivo para conexión a la base de datos

// Seleccionar base de datos
$conn->select_db('sag');

// Verificar si ya existe un superusuario
$result = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'superusuario'");
$row = $result->fetch_assoc();
if ($row['count'] > 0) {
    die("Ya existe un superusuario registrado. Elimina o desactiva este archivo.");
}

// Manejar el formulario de instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insertar el primer usuario
    $stmt = $conn->prepare("INSERT INTO users (id_client, username, password, role) VALUES (?, ?, ?, ?)");
    $id_client = 0; // Valor temporal para id_client
    $role = 'superusuario'; // Definir el rol aquí para evitar pasar por referencia
    $stmt->bind_param("isss", $id_client, $username, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Superusuario registrado correctamente.";
    } else {
        echo "Error al registrar el superusuario: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Instalación</h1>
    </header>
    <div class="install-container">
        <h2>Registrar Primer Usuario</h2>
        <form action="install.php" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Registrar Superusuario</button>
        </form>
    </div>
</body>
</html>

