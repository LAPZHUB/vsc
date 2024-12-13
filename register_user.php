<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

// Obtener la lista de clientes
$stmt = $conn->prepare("SELECT id_client, name FROM clients");
$stmt->execute();
$result = $stmt->get_result();
$clientes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $id_client = $_POST['id_client'];

    // Preparar la consulta para insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, id_client) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $password, $role, $id_client);

    if ($stmt->execute()) {
        $message = "Usuario registrado correctamente.";
    } else {
        if ($stmt->errno == 1062) { // Código de error para entrada duplicada
            $message = "Error: El nombre de usuario ya está registrado.";
        } else {
            $message = "Error al registrar el usuario: " . $stmt->error;
        }
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
    <title>Registrar Nuevo Usuario</title>
    <link rel="stylesheet" href="styles.css"> <!-- Estilos generales -->
    <link rel="stylesheet" href="ruser.css"> <!-- Estilos específicos para register_user -->
</head>
<body>
    <header>
        <h1>Registrar Nuevo Usuario</h1>
    </header>
    <div class="form-container">
        <form action="register_user.php" method="POST" onsubmit="return showModal(this)">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="role">Rol:</label>
            <select id="role" name="role" required>
                <option value="consulta">Consulta</option>
                <option value="escritura">Escritura</option>
                <option value="superusuario">Superusuario</option>
            </select>
            
            <label for="id_client">Cliente:</label>
            <select id="id_client" name="id_client" required>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?php echo $cliente['id_client']; ?>"><?php echo $cliente['name']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit">Registrar Usuario</button>
        </form>
        <button onclick="goBack()">Regresar</button> <!-- Botón de regresar -->
    </div>

    <!-- Ventana Flotante -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modalMessage"><?php echo isset($message) ? $message : ''; ?></p>
        </div>
    </div>

    <script>
        function showModal(form) {
            const modal = document.getElementById("myModal");
            const message = document.getElementById("modalMessage");

            // Aquí puedes personalizar el mensaje a mostrar en la ventana flotante
            message.textContent = "Procesando el registro, por favor espera...";

            modal.style.display = "block";
            return true; // Permite el envío del formulario
        }

        function closeModal() {
            const modal = document.getElementById("myModal");
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById("myModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function goBack() { 
            window.history.back(); 
        }

        // Mostrar modal si hay un mensaje
        <?php if (isset($message)): ?>
            document.addEventListener('DOMContentLoaded', (event) => {
                document.getElementById("myModal").style.display = "block";
            });
        <?php endif; ?>
    </script>
</body>
</html>
