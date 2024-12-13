<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Preparar la consulta para insertar el nuevo cliente
    $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $address);

    if ($stmt->execute()) {
        $message = "Cliente registrado correctamente.";
    } else {
        if ($stmt->errno == 1062) { // Código de error para entrada duplicada
            $message = "Error: El correo electrónico ya está registrado.";
        } else {
            $message = "Error al registrar el cliente: " . $stmt->error;
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
    <title>Registrar Nuevo Cliente</title>
    <link rel="stylesheet" href="rclient.css">
    <style>
        /* Estilos para la ventana flotante */
        .modal {
            display: none; /* Oculto por defecto */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4); /* Fondo oscuro */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Registrar Nuevo Cliente</h1>
    </header>
    <div class="form-container">
        <form action="register_client.php" method="POST" onsubmit="return showModal(this)">
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="phone">Teléfono:</label>
            <input type="tel" id="phone" name="phone">
            
            <label for="address">Dirección:</label>
            <textarea id="address" name="address"></textarea>
            
            <button type="submit">Registrar Cliente</button>
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
