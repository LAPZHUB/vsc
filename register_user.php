<?php
session_start();
include 'db.php'; // Conexión a la base de datos
include 'security.php'; // Verificar la seguridad del sistema

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

// Obtener los nombres de los clientes para el menú desplegable
$clientes = [];
$query = "SELECT id, name FROM clients ORDER BY name";
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
    <link rel="stylesheet" href="css/ruser.css">
    <title>Registrar Usuario</title>
</head>
<body>
    <div class="form-container">
        <h2>Registrar Usuario</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"> <?php echo $error; ?> </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"> <?php echo $success; ?> </div>
        <?php endif; ?>

        <form action="register_user.php" method="POST">
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="role">Rol:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="admin">Administrador</option>
                    <option value="editor">Editor</option>
                    <option value="viewer">Visualizador</option>
                </select>
            </div>

            <div class="form-group">
                <label for="id_client">Cliente:</label>
                <select id="id_client" name="id_client" class="form-control" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['id']; ?>">
                            <?php echo $cliente['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="activo">
                    <input type="checkbox" id="activo" name="activo">
                    Usuario Activo
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
        </form>
    </div>
</body>
</html>
