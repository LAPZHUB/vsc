<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

// Obtener los nombres de los clientes para el menú desplegable
$clientes = [];
$query = "SELECT id_client, name FROM clients ORDER BY name";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $clientes[] = $row;
}

// Procesar el formulario al enviarse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $id_client = $_POST['id_client'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Validar que el nombre de usuario no esté duplicado
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "El nombre de usuario ya existe.";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]*$/", $username)) {
        $error = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
    } else {
        // Insertar el nuevo usuario
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, id_client, activo, date_create) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssii", $username, $hashed_password, $role, $id_client, $activo);

        if ($stmt->execute()) {
            $success = "Usuario registrado exitosamente.";
        } else {
            $error = "Error al registrar el usuario: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nuevo Usuario</title>
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
                <li><a href="admin.php">Inicio</a></li>
                <li><a href="register_user.php" class="active">Registrar Usuario</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="form-container">
            <h1>Registrar Nuevo Usuario</h1>
            <form action="register_user.php" method="POST" id="userForm">
                <div class="form-group">
                    <label for="username">Nombre de Usuario:</label>
                    <input type="text" id="username" name="username" placeholder="Nombre de usuario" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" placeholder="Contraseña" required>
                </div>
                <div class="form-group">
                    <label for="role">Rol:</label>
                    <select id="role" name="role" required>
                        <option value="Administrador">Administrador</option>
                        <option value="Usuario">Usuario</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="client">Cliente:</label>
                    <select id="client" name="client">
                        <option value="">Seleccione un cliente</option>
                        <!-- Opciones cargadas dinámicamente -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="activo">Usuario Activo:</label>
                    <input type="checkbox" id="activo" name="activo">
                </div>
                <button type="submit" class="button">Registrar Usuario</button>
                <a href="admin.php" class="button-secondary">Regresar</a>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Sistema SAG. Todos los derechos reservados.</p>
    </footer>
</body>
</html>