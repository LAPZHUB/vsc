<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $direccion_general = $_POST['direccion_general'];
    $escrito_solicitud = $_POST['escrito_solicitud'];
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
    $geolocalizacion = $_POST['geolocalizacion'];

    // Preparar la consulta para insertar el nuevo servicio
    $stmt = $conn->prepare("INSERT INTO servicios (direccion_general, escrito_solicitud, fecha_captura, area_responsable, nombre_completo, clave_elector, telefono, calle, num_interior, num_exterior, colonia, codigo_postal, num_personas, cumplido, seccion_electoral, geolocalizacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssisss", $direccion_general, $escrito_solicitud, $fecha_captura, $area_responsable, $nombre_completo, $clave_elector, $telefono, $calle, $num_interior, $num_exterior, $colonia, $codigo_postal, $num_personas, $cumplido, $seccion_electoral, $geolocalizacion);

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
    <title>Registrar Nuevo Servicio</title>
    <link rel="stylesheet" href="rservice.css">
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
        <h1>Registrar Nuevo Servicio</h1>
    </header>
    <div class="form-container">
        <form action="register_service.php" method="POST" onsubmit="return showModal(this)">
            <label for="direccion_general">Dirección General:</label>
            <input type="text" id="direccion_general" name="direccion_general" required>

            <label for="escrito_solicitud">Escrito Solicitud:</label>
            <input type="text" id="escrito_solicitud" name="escrito_solicitud">

            <label for="fecha_captura">Fecha de Captura:</label>
            <input type="date" id="fecha_captura" name="fecha_captura" required>
            
            <label for="area_responsable">Área Responsable:</label>
            <input type="text" id="area_responsable" name="area_responsable" required>

            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" required>

            <label for="clave_elector">Clave Elector:</label>
            <input type="text" id="clave_elector" name="clave_elector">

            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono">

            <label for="calle">Calle:</label>
            <input type="text" id="calle" name="calle">

            <label for="num_interior">Número Interior:</label>
            <input type="text" id="num_interior" name="num_interior">

            <label for="num_exterior">Número Exterior:</label>
            <input type="text" id="num_exterior" name="num_exterior">

            <label for="colonia">Colonia:</label>
            <input type="text" id="colonia" name="colonia">

            <label for="codigo_postal">Código Postal:</label>
            <input type="text" id="codigo_postal" name="codigo_postal">

            <label for="num_personas">Número de Personas:</label>
            <input type="number" id="num_personas" name="num_personas">

            <label for="cumplido">Cumplido:</label>
            <input type="checkbox" id="cumplido" name="cumplido">

            <label for="seccion_electoral">Sección Electoral:</label>
            <input type="text" id="seccion_electoral" name="seccion_electoral">

            <label for="geolocalizacion">Geolocalización:</label>
            <input type="text" id="geolocalizacion" name="geolocalizacion">

            <button type="submit">Registrar Servicio</button>
            <button type="button" onclick="window.location.href='register_service_doc.php'">Subir Documentos</button> <!-- Botón para ir a register_service_doc.php -->
            <button type="button" onclick="goBack()">Regresar</button> <!-- Botón de regresar -->
        </form>
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

<h4>Ubicación del Servicio</h4>
<div class="mb-3">
    <label for="estado" class="form-label">Estado</label>
    <select id="estado_servicio" name="estado_servicio" class="form-select" required>
        <option value="">Seleccione un estado</option>
        <?php
        $query = "SELECT id, nombre FROM estados";
        $result = mysqli_query($conexion, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
        }
        ?>
    </select>
</div>
<div class="mb-3">
    <label for="municipio" class="form-label">Municipio</label>
    <select id="municipio_servicio" name="municipio_servicio" class="form-select" required>
        <option value="">Seleccione un municipio</option>
    </select>
</div>
<div class="mb-3">
    <label for="colonia" class="form-label">Colonia</label>
    <select id="colonia" name="colonia" class="form-select" required>
        <option value="">Seleccione una colonia</option>
    </select>
</div>
<div class="mb-3">
    <label for="seccion" class="form-label">Sección</label>
    <select id="seccion" name="seccion" class="form-select" required>
        <option value="">Seleccione una sección</option>
    </select>
</div>

<script>
document.getElementById('estado').addEventListener('change', function () {
    const estadoId = this.value;
    const municipioSelect = document.getElementById('municipio');

    municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
    if (estadoId) {
        fetch(`get_municipios.php?estado_id=${estadoId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(municipio => {
                    const option = document.createElement('option');
                    option.value = municipio.id;
                    option.textContent = municipio.nombre;
                    municipioSelect.appendChild(option);
                });
            });
    }
});

document.getElementById('municipio').addEventListener('change', function () {
    const municipioId = this.value;
    const coloniaSelect = document.getElementById('colonia');

    coloniaSelect.innerHTML = '<option value="">Seleccione una colonia</option>';
    if (municipioId) {
        fetch(`get_colonias.php?municipio_id=${municipioId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(colonia => {
                    const option = document.createElement('option');
                    option.value = colonia.id;
                    option.textContent = colonia.nombre;
                    coloniaSelect.appendChild(option);
                });
            });
    }
});
</script>


</body>
</html>

