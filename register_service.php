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
    $ID_ESTADO = $_POST['estado'];
    $ID_MUNICIPIO = $_POST['municipio'];
    $NOMBRE_COLONIA = $_POST['colonia'];
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
        $stmt = $conn->prepare("INSERT INTO servicios (direccion_general, fecha_captura, area_responsable, nombre_completo, clave_elector, telefono, calle, num_interior, num_exterior, ID_ESTADO, ID_MUNICIPIO, COLONIA, codigo_postal, num_personas, cumplido, seccion_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssiiiisiii", $direccion_general, $fecha_captura, $area_responsable, $nombre_completo, $clave_elector, $telefono, $calle, $num_interior, $num_exterior, $ID_ESTADO, $ID_MUNICIPIO, $NOMBRE_COLONIA, $codigo_postal, $num_personas, $cumplido, $seccion_id);

        if ($stmt->execute()) {
            header('Location: register_service_doc.php?id=' . $conn->insert_id);
            exit();
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

<!-- HTML -->
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
                
                    // Cargar colonias
                    $.ajax({
                        url: 'ajax.php',
                        method: 'POST',
                        data: { action: 'get_colonias', ID_MUNICIPIO: ID_MUNICIPIO },
                        success: function (data) {
                            $('#colonia').html('<option value="">Seleccione una colonia</option>' + data);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('Error al cargar colonias:', textStatus, errorThrown);
                        }
                    });
               
               
                     // Cargar secciones
                     $.ajax({
                        url: 'ajax.php',
                        method: 'POST',
                        data: { action: 'get_secciones', ID_MUNICIPIO: ID_MUNICIPIO },
                        success: function (data) {
                            $('#seccion').html('<option value="">Seleccione una sección</option>' + data);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('Error al cargar secciones:', textStatus, errorThrown);
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
                <label for="codigo_postal">Código Postal:</label>
                <input type="text" id="codigo_postal" name="codigo_postal" required>
            </div>
            <div class="form-group">
                <label for="num_personas">Número de Personas:</label>
                <input type="number" id="num_personas" name="num_personas" required>
            </div>
            <div class="form-group">
                <label for="cumplido">Cumplido:</label>
                <input type="checkbox" id="cumplido" name="cumplido">
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