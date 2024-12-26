<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $direccion_general = $_POST['direccion_general'];
    $fecha_captura = $_POST['fecha_captura'];
    $area_responsable = $_POST['area_responsable'];
    $nombre_completo = $_POST['nombre_completo'];
    $clave_elector = $_POST['clave_elector'];
    $telefono = $_POST['telefono'];
    $calle = $_POST['calle'];
    $num_interior = $_POST['num_interior'];
    $num_exterior = $_POST['num_exterior'];
    $estado_id = $_POST['estado'];
    $municipio_id = $_POST['municipio'];
    $colonia_id = $_POST['colonia'];
    $codigo_postal = $_POST['codigo_postal'];
    $num_personas = $_POST['num_personas'];
    $cumplido = isset($_POST['cumplido']) ? 1 : 0;
    $seccion_id = $_POST['seccion'];

    // Validaciones
    if (empty($direccion_general) || empty($fecha_captura) || empty($area_responsable) || empty($nombre_completo) || empty($clave_elector) || empty($telefono) || empty($calle) || empty($num_exterior) || empty($estado_id) || empty($municipio_id) || empty($colonia_id) || empty($codigo_postal) || empty($num_personas) || empty($seccion_id)) {
        $message = "Por favor completa todos los campos obligatorios.";
    } else {
        // Insertar en la base de datos
        $stmt = $conn->prepare("INSERT INTO servicios (direccion_general, fecha_captura, area_responsable, nombre_completo, clave_elector, telefono, calle, num_interior, num_exterior, estado_id, municipio_id, colonia_id, codigo_postal, num_personas, cumplido, seccion_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssiiiisiii", $direccion_general, $fecha_captura, $area_responsable, $nombre_completo, $clave_elector, $telefono, $calle, $num_interior, $num_exterior, $estado_id, $municipio_id, $colonia_id, $codigo_postal, $num_personas, $cumplido, $seccion_id);

        if ($stmt->execute()) {
            $message = "Servicio registrado correctamente.";
        } else {
            $message = "Error al registrar el servicio: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Manejo de AJAX para cargar estados, municipios, colonias y secciones dinámicamente
if (isset($_GET['action']) && $_GET['action'] === 'get_estados') {
    $query = "SELECT id, nombre FROM estados ORDER BY nombre";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        echo "<option value=\"" . $row['id'] . "\">" . $row['nombre'] . "</option>";
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'get_municipios') {
    $estado_id = $_POST['estado_id'];
    $query = "SELECT id, nombre FROM municipios WHERE estado_id = ? ORDER BY nombre";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $estado_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo "<option value=\"" . $row['id'] . "\">" . $row['nombre'] . "</option>";
    }
    $stmt->close();
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'get_colonias') {
    $municipio_id = $_POST['municipio_id'];
    $query = "SELECT id, nombre FROM colonias WHERE municipio_id = ? ORDER BY nombre";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $municipio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo "<option value=\"" . $row['id'] . "\">" . $row['nombre'] . "</option>";
    }
    $stmt->close();
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'get_secciones') {
    $colonia_id = $_POST['colonia_id'];
    $query = "SELECT id, nombre FROM secciones WHERE colonia_id = ? ORDER BY nombre";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $colonia_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo "<option value=\"" . $row['id'] . "\">" . $row['nombre'] . "</option>";
    }
    $stmt->close();
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nuevo Servicio</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <script>
        $(document).ready(function () {
            // Cargar estados al cargar la página
            $.ajax({
                url: 'register_service.php?action=get_estados',
                method: 'GET',
                success: function (data) {
                    $('#estado').append(data);
                }
            });

            // Cargar municipios al seleccionar un estado
            $('#estado').change(function () {
                const estadoId = $(this).val();
                if (estadoId) {
                    $.ajax({
                        url: 'register_service.php',
                        method: 'POST',
                        data: { action: 'get_municipios', estado_id: estadoId },
                        success: function (data) {
                            $('#municipio').html('<option value="">Seleccione un municipio</option>' + data);
                            $('#colonia').html('<option value="">Seleccione una colonia</option>');
                            $('#seccion').html('<option value="">Seleccione una sección</option>');
                        }
                    });
                }
            });

            // Cargar colonias al seleccionar un municipio
            $('#municipio').change(function () {
                const municipioId = $(this).val();
                if (municipioId) {
                    $.ajax({
                        url: 'register_service.php',
                        method: 'POST',
                        data: { action: 'get_colonias', municipio_id: municipioId },
                        success: function (data) {
                            $('#colonia').html('<option value="">Seleccione una colonia</option>' + data);
                            $('#seccion').html('<option value="">Seleccione una sección</option>');
                        }
                    });
                }
            });

            // Cargar secciones al seleccionar una colonia
            $('#colonia').change(function () {
                const coloniaId = $(this).val();
                if (coloniaId) {
                    $.ajax({
                        url: 'register_service.php',
                        method: 'POST',
                        data: { action: 'get_secciones', colonia_id: coloniaId },
                        success: function (data) {
                            $('#seccion').html('<option value="">Seleccione una sección</option>' + data);
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
                <li><a href="register_service.php" class="active">Registrar Servicio</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="form-container">
            <h1>Registrar Nuevo Servicio</h1>
            <form action="register_service.php" method="POST" id="serviceForm">
                <div class="form-group">
                    <label for="direccion_general">Dirección General:</label>
                    <input type="text" id="direccion_general" name="direccion_general" placeholder="Ingrese la dirección general" required>
                </div>
                <div class="form-group">
                    <label for="fecha_captura">Fecha de Captura:</label>
                    <input type="date" id="fecha_captura" name="fecha_captura" required>
                </div>
                <div class="form-group">
                    <label for="area_responsable">Área Responsable:</label>
                    <input type="text" id="area_responsable" name="area_responsable" placeholder="Ingrese el área responsable" required>
                </div>
                <div class="form-group">
                    <label for="nombre_completo">Nombre Completo:</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" placeholder="Nombre completo del solicitante" required>
                </div>
                <div class="form-group">
                    <label for="clave_elector">Clave de Elector:</label>
                    <input type="text" id="clave_elector" name="clave_elector" placeholder="Clave de elector" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" placeholder="Número de teléfono" required>
                </div>
                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado">
                        <option value="">Seleccione un estado</option>
                        <!-- Opciones dinámicas -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="municipio">Municipio:</label>
                    <select id="municipio" name="municipio">
                        <option value="">Seleccione un municipio</option>
                        <!-- Opciones dinámicas -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="colonia">Colonia:</label>
                    <select id="colonia" name="colonia">
                    
                </div>
                <div class="form-group">
                    <label for="codigo_postal">Código Postal:</label>
                    <input type="text" id="codigo_postal" name="codigo_postal" placeholder="Código postal" required>
                </div>
                <button type="submit" class="button">Registrar Servicio</button>
                <a href="admin.php" class="button-secondary">Regresar</a>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Sistema SAG. Todos los derechos reservados.</p>
    </footer>
</body>
</html>