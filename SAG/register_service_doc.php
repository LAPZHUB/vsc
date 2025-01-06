<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

// Función para crear el directorio del cliente
function crearDirectorioCliente($id_cliente) {
    $uploads_dir = "uploads/cliente_$id_cliente/";
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }
    return $uploads_dir;
}

// Función para procesar archivos
function procesarArchivo($archivo, $uploads_dir, $campo, $id_cliente, $id_usuario) {
    if ($archivo && $archivo['tmp_name']) {
        $timestamp = date('Y-m-d_H-i-s');
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);

        // Validación del formato del archivo
        $formatos_permitidos = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array(strtolower($extension), $formatos_permitidos)) {
            return "Formato no permitido para $campo. Solo se permiten: " . implode(', ', $formatos_permitidos);
        }

        $nombre_unico = "{$campo}_{$id_cliente}_{$id_usuario}_{$timestamp}.$extension";
        $ruta_destino = $uploads_dir . $nombre_unico;
        move_uploaded_file($archivo['tmp_name'], $ruta_destino);
        return $ruta_destino;
    }
    return null;
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];
    $id_cliente = $_POST['id_cliente']; // ID del cliente
    $id_usuario = $_SESSION['user_id']; // ID del usuario

    $escrito_solicitud_doc = $_FILES['escrito_solicitud_doc'];
    $ine_anverso = $_FILES['ine_anverso'];
    $ine_reverso = $_FILES['ine_reverso'];
    $comprobante_domicilio = $_FILES['comprobante_domicilio'];
    $foto_lugar = $_FILES['foto_lugar'];
    $otro_doc = isset($_FILES['otro_doc']) ? $_FILES['otro_doc'] : null;

    // Validaciones
    if (empty($latitud) || empty($longitud) || empty($id_cliente)) {
        $message = "Por favor completa todos los campos obligatorios.";
    } else {
        // Crear carpeta específica para el cliente
        $uploads_dir = crearDirectorioCliente($id_cliente);

        // Procesar documentos
        $escrito_solicitud_doc_path = procesarArchivo($escrito_solicitud_doc, $uploads_dir, 'escrito_solicitud_doc', $id_cliente, $id_usuario);
        $ine_anverso_path = procesarArchivo($ine_anverso, $uploads_dir, 'ine_anverso', $id_cliente, $id_usuario);
        $ine_reverso_path = procesarArchivo($ine_reverso, $uploads_dir, 'ine_reverso', $id_cliente, $id_usuario);
        $comprobante_domicilio_path = procesarArchivo($comprobante_domicilio, $uploads_dir, 'comprobante_domicilio', $id_cliente, $id_usuario);
        $foto_lugar_path = procesarArchivo($foto_lugar, $uploads_dir, 'foto_lugar', $id_cliente, $id_usuario);
        $otro_doc_path = procesarArchivo($otro_doc, $uploads_dir, 'otro_doc', $id_cliente, $id_usuario);

        // Actualizar en la base de datos
        $stmt = $conn->prepare("UPDATE servicios SET latitud = ?, longitud = ?, escrito_solicitud_doc = ?, ine_anverso = ?, ine_reverso = ?, comprobante_domicilio = ?, foto_lugar = ?, otro_doc = ? WHERE id = ?");
        $stmt->bind_param("ddssssssi", $latitud, $longitud, $escrito_solicitud_doc_path, $ine_anverso_path, $ine_reverso_path, $comprobante_domicilio_path, $foto_lugar_path, $otro_doc_path, $_POST['servicio_id']);

        if ($stmt->execute()) {
            $message = "Documentos registrados correctamente.";
        } else {
            $message = "Error al registrar los documentos: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Documentación de Servicio</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARV6H4a893nq_zdAVM0nIMv00uCoY-RDQ&libraries=places"></script>
    <script>
        function initMap() {
            const defaultLocation = { lat: 19.4326, lng: -99.1332 }; // Ciudad de México
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: defaultLocation
            });

            const marker = new google.maps.Marker({
                position: defaultLocation,
                map: map,
                draggable: true
            });

            google.maps.event.addListener(marker, 'dragend', function () {
                document.getElementById('latitud').value = marker.getPosition().lat();
                document.getElementById('longitud').value = marker.getPosition().lng();
            });
        }
    </script>
</head>
<body onload="initMap()">
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
    <Mmain> 
            <div class="form-container">
                <h1>Registrar Documentos Complementarios</h1>
                <form action="register_service_doc.php" method="POST" enctype="multipart/form-data" id="serviceDocForm">
                    <fieldset>
                        <legend>Documentos del Servicio</legend>
                        <div class="form-group">
                            <label for="escrito_solicitud_doc">Escrito de Solicitud:</label>
                            <input type="file" id="escrito_solicitud_doc" name="escrito_solicitud_doc" required>
                        </div>
                        <div class="form-group">
                            <label for="ine_anverso">INE (Anverso):</label>
                            <input type="file" id="ine_anverso" name="ine_anverso" required>
                        </div>
                        <div class="form-group">
                            <label for="ine_reverso">INE (Reverso):</label>
                            <input type="file" id="ine_reverso" name="ine_reverso" required>
                        </div>
                        <div class="form-group">
                            <label for="foto_lugar">Fotografía del Lugar:</label>
                            <input type="file" id="foto_lugar" name="foto_lugar">
                        </div>
                        <div class="form-group">
                            <label for="comprobante_domicilio">Comprobante de Domicilio:</label>
                            <input type="file" id="comprobante_domicilio" name="comprobante_domicilio">
                        </div>
                        <div class="form-group">
                            <label for="otro_doc">Otro Documento:</label>
                            <input type="file" id="otro_doc" name="otro_doc">
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Ubicación</legend>
                        <div id="map" style="height: 300px; width: 100%; margin-bottom: 20px;"></div>
                        <div class="form-group">
                            <label for="latitud">Latitud:</label>
                            <input type="text" id="latitud" name="latitud" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="longitud">Longitud:</label>
                            <input type="text" id="longitud" name="longitud" readonly required>
                        </div>
                    </fieldset>
                    <button type="submit" class="button">Registrar Documentación</button>
                    <a href="register_service.php" class="button-secondary">Regresar</a>
                </form>
            </div>
        <main>    
    <footer>
        <p>&copy; 2024 Sistema SAG. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
