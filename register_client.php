<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;

    // Validar que el campo 'name' no esté vacío
    if (is_null($name) || $name === '') {
        die('Error: El campo "Nombre" es obligatorio.');
    
    }
    if (empty($email)) {
        die('Error: El campo "Email" es obligatorio.');
    }
    // Otros datos
    $ID_ESTADO = isset($_POST['estado']) ? intval($_POST['estado']) : null;
    $DF = isset($_POST['distrito_federal']) ? intval($_POST['distrito_federal']) : null;
    $DL = isset($_POST['distrito_local']) ? intval($_POST['distrito_local']) : null;
    $ID_MUNICIPIO = isset($_POST['municipio']) ? intval($_POST['municipio']) : null;

    // Inserción en la base de datos
    $query = "INSERT INTO clients (name, email, phone, address, ID_ESTADO, ID_MUNICIPIO, DF, DL) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssisiiii", $name, $email, $phone, $address, $ID_ESTADO, $ID_MUNICIPIO, $DF, $DL);

    if ($stmt->execute()) {
        echo "Cliente registrado correctamente.";
    } else {
        echo "Error al registrar el cliente: " . $stmt->error;
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
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <script>
        $(document).ready(function () {
            // Cargar estados al cargar la página
            $.ajax({
                url: 'ajax.php?action=get_estado',
                method: 'GET',
                success: function (data) {
                    $('#estado').append(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Error al cargar estados:', textStatus, errorThrown);
                }
            });

            // Manejar cambios en el estado
            $('#estado').change(function () {
                const ID_ESTADO = $(this).val();
                if (ID_ESTADO) {
                    // Cargar distritos federales
                    $.ajax({
                        url: 'ajax.php',
                        method: 'POST',
                        data: { action: 'get_df', ID_ESTADO: ID_ESTADO },
                        success: function (data) {
                            $('#distrito_federal').html('<option value="">Seleccione un distrito federal</option>' + data);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('Error al cargar distritos federales:', textStatus, errorThrown);
                        }
                    });

                    // Cargar distritos locales
                    $.ajax({
                        url: 'ajax.php',
                        method: 'POST',
                        data: { action: 'get_dl', ID_ESTADO: ID_ESTADO },
                        success: function (data) {
                            $('#distrito_local').html('<option value="">Seleccione un distrito local</option>' + data);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('Error al cargar distritos locales:', textStatus, errorThrown);
                        }
                    });

                    // Cargar municipios
                    $.ajax({
                        url: 'ajax.php',
                        method: 'POST',
                        data: { action: 'get_municipios', ID_ESTADO: ID_ESTADO },
                        success: function (data) {
                            $('#municipio').html('<option value="">Seleccione un municipio</option>' + data);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('Error al cargar municipios:', textStatus, errorThrown);
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>
    <header>
        <div class="logo">Sistema SAG</div>
        <nav>
            <ul>
                <li><a href="admin.php">Inicio</a></li>
                <li><a href="register_client.php" class="active">Registrar Cliente</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="form-container">
            <h1>Registrar Nuevo Cliente</h1>
            <form action="register_client.php" method="POST" id="clientForm">
                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input type="text" id="name" name="name" placeholder="Nombre completo" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" placeholder="Correo electrónico" required>
                </div>
                <div class="form-group">
                    <label for="phone">Teléfono:</label>
                    <input type="text" id="phone" name="phone" placeholder="Teléfono" required>
                </div>
                <div class="form-group">
                    <label for="address">Dirección:</label>
                    <textarea id="address" name="address" placeholder="Dirección completa" required></textarea>
                </div>
                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado">
                        <option value="">Seleccione un estado</option>
                        <!-- Opciones dinámicas cargadas con AJAX -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="distrito_federal">Distrito Federal:</label>
                    <select id="distrito_federal" name="distrito_federal">
                        <option value="">Seleccione un distrito federal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="distrito_local">Distrito Local:</label>
                    <select id="distrito_local" name="distrito_local">
                        <option value="">Seleccione un distrito local</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="municipio">Municipio:</label>
                    <select id="municipio" name="municipio">
                        <option value="">Seleccione un municipio</option>
                    </select>
                </div>
                <button type="submit" class="button">Registrar Cliente</button>
                <a href="admin.php" class="button-secondary">Regresar</a>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Sistema SAG. Todos los derechos reservados.</p>
    </footer>
</body>
</html>