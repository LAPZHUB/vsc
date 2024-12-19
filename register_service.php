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
    $colonia = $_POST['colonia'];
    $codigo_postal = $_POST['codigo_postal'];
    $num_personas = $_POST['num_personas'];
    $cumplido = isset($_POST['cumplido']) ? 1 : 0;
    $seccion_electoral = $_POST['seccion_electoral'];

    // Preparar la consulta para insertar el nuevo servicio
    $stmt = $conn->prepare("INSERT INTO servicios (direccion_general, fecha_captura, area_responsable, nombre_completo, clave_elector, telefono, calle, num_interior, num_exterior, colonia, codigo_postal, num_personas, cumplido, seccion_electoral) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssiis", $direccion_general, $fecha_captura, $area_responsable, $nombre_completo, $clave_elector, $telefono, $calle, $num_interior, $num_exterior, $colonia, $codigo_postal, $num_personas, $cumplido, $seccion_electoral);

    if ($stmt->execute()) {
        $message = "Servicio registrado correctamente.";
    } else {
        $message = "Error al registrar el servicio: " . $stmt->error;
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
    <title>Registrar Servicio</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="form-container">
        <h1>Registrar Servicio</h1>
        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
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
                    <?php
                    // Consulta para obtener los estados
                    include 'db.php';
                    $query_estados = "SELECT ID_ESTADO, NOMBRE_ESTADO FROM estados ORDER BY NOMBRE_ESTADO";
                    $result_estados = $conn->query($query_estados);
                    while ($row = $result_estados->fetch_assoc()):
                    ?>
                        <option value="<?php echo $row['ID_ESTADO']; ?>"><?php echo $row['NOMBRE_ESTADO']; ?></option>
                    <?php endwhile; ?>
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
                <label for="seccion_electoral">Sección Electoral:</label>
                <select id="seccion_electoral" name="seccion_electoral" required>
                    <option value="">Seleccione una sección</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit">Registrar Servicio</button>
            </div>
        </form>
    </div>

    <script>
        // Cargar municipios al seleccionar estado
        $('#estado').change(function() {
            const estadoId = $(this).val();
            if (estadoId) {
                $.ajax({
                    url: 'get_municipios.php',
                    type: 'POST',
                    data: { estado: estadoId },
                    success: function(data) {
                        $('#municipio').html(data);
                        $('#colonia').html('<option value="">Seleccione una colonia</option>');
                        $('#seccion_electoral').html('<option value="">Seleccione una sección</option>');
                    }
                });
            }
        });

        // Cargar colonias al seleccionar municipio
        $('#municipio').change(function() {
            const municipioId = $(this).val();
            if (municipioId) {
                $.ajax({
                    url: 'get_colonias.php',
                    type: 'POST',
                    data: { municipio: municipioId },
                    success: function(data) {
                        $('#colonia').html(data);
                        $('#seccion_electoral').html('<option value="">Seleccione una sección</option>');
                    }
                });
            }
        });

        // Cargar secciones al seleccionar colonia
        $('#colonia').change(function() {
            const coloniaId = $(this).val();
            if (coloniaId) {
                $.ajax({
                    url: 'get_secciones.php',
                    type: 'POST',
                    data: { colonia: coloniaId },
                    success: function(data) {
                        $('#seccion_electoral').html(data);
                    }
                });
            }
        });
    </script>
</body>
</html>