<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar autenticación y rol
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

    // Validación de campos
    if (empty($direccion_general) || empty($fecha_captura) || empty($area_responsable) || empty($nombre_completo) ||
        empty($clave_elector) || empty($telefono) || empty($calle) || empty($num_exterior) || 
        empty($estado_id) || empty($municipio_id) || empty($colonia_id) || empty($codigo_postal) ||
        empty($num_personas) || empty($seccion_id)) {
        $message = "Por favor completa todos los campos obligatorios.";
    } else {
        // Insertar datos en la base de datos
        $stmt = $conn->prepare("INSERT INTO servicios (direccion_general, fecha_captura, area_responsable, nombre_completo, clave_elector, telefono, calle, num_interior, num_exterior, estado_id, municipio_id, colonia_id, codigo_postal, num_personas, cumplido, seccion_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssiiiisiii", $direccion_general, $fecha_captura, $area_responsable, $nombre_completo, $clave_elector, $telefono, $calle, $num_interior, $num_exterior, $estado_id, $municipio_id, $colonia_id, $codigo_postal, $num_personas, $cumplido, $seccion_id);

        if ($stmt->execute()) {
            header('Location: register_service_doc.php?id=' . $conn->insert_id);
            exit();
        } else {
            $message = "Error al registrar el servicio: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
  // Cargar estados al cargar la página
<script>
    $(document).ready(function () {
        // Cargar estados al cargar la página
        $.ajax({
            url: 'get_estados.php',
            method: 'GET',
            success: function (data) {
                $('#estado').html(data);
            }
        });

        // Cargar municipios al seleccionar un estado
        $('#estado').change(function () {
            const estadoId = $(this).val();
            if (estadoId) {
                $.ajax({
                    url: 'get_municipios.php',
                    method: 'POST',
                    data: { estado_id: estadoId },
                    success: function (data) {
                        $('#municipio').html(data);
                        $('#colonia').html('<option value="">Seleccione una colonia</option>');
                        $('#seccion').html('<option value="">Seleccione una sección</option>');
                    }
                });
            } else {
                $('#municipio').html('<option value="">Seleccione un municipio</option>');
                $('#colonia').html('<option value="">Seleccione una colonia</option>');
                $('#seccion').html('<option value="">Seleccione una sección</option>');
            }
        });

        // Cargar colonias y secciones al seleccionar un municipio
        $('#municipio').change(function () {
            const municipioId = $(this).val();
            if (municipioId) {
                // Cargar colonias
                $.ajax({
                    url: 'get_colonias.php',
                    method: 'POST',
                    data: { municipio_id: municipioId },
                    success: function (data) {
                        $('#colonia').html(data);
                    }
                });
                // Cargar secciones
                $.ajax({
                    url: 'get_secciones.php',
                    method: 'POST',
                    data: { municipio_id: municipioId },
                    success: function (data) {
                        $('#seccion').html(data);
                    }
                });
            } else {
                $('#colonia').html('<option value="">Seleccione una colonia</option>');
                $('#seccion').html('<option value="">Seleccione una sección</option>');
            }
        });
    });
</script>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nuevo Servicio</title>
    <link rel="stylesheet" href="styles.css">
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
        <form action="register_service.php" method="POST">
            <div class="form-group">
                <label for="direccion_general">Dirección General:</label>
                <input type="text" id="direccion_general" name="direccion_general" required>
            </div>
            <div class="form-group">
                <label for="fecha_captura">Fecha de Captura:</label>
                <input type="date" id="fecha_captura" name="fecha_captura" required>
            </div>
            <div class="form-group">
                <label for="area_responsable">Área Responsable:</label>
                <input type="text" id="area_responsable" name="area_responsable" required>
            </div>
            <div class="form-group">
                <label for="nombre_completo">Nombre Completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" required>
            </div>
            <div class="form-group">
                <label for="clave_elector">Clave de Elector:</label>
                <input type="text" id="clave_elector" name="clave_elector" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="calle">Calle:</label>
                <input type="text" id="calle" name="calle" required>
            </div>
            <div class="form-group">
                <label for="num_interior">Número Interior:</label>
                <input type="text" id="num_interior" name="num_interior">
            </div>
            <div class="form-group">
                <label for="num_exterior">Número Exterior:</label>
                <input type="text" id="num_exterior" name="num_exterior" required>
            </div>
            <div class="form-group">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="">Seleccione un estado</option>
                </select>
            </div>
            <div class="form-group">
                <label for="municipio">Municipio:</label>
                <select id="municipio" name="municipio" required>
                    <option value="">Seleccione un municipio</option>
                </select>
            </div>
            <div class="form-group">
                <label for="colonia">Colonia:</label>
                <select id="colonia" name="colonia" required>
                    <option value="">Seleccione una colonia</option>
                </select>
            </div>
            <div class="form-group">
                <label for="seccion">Sección:</label>
                <select id="seccion" name="seccion" required>
                    <option value="">Seleccione una sección</option>
                </select>
            </div>
            <button type="submit">Registrar Servicio</button>
        </form>
    </section>
</main>
<footer>
    <p>&copy; 2024 Sistema SAG. Todos los derechos reservados.</p>
</footer>
</body>
</html>