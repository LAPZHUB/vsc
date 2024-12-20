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
        $uploads_dir = "uploads/cliente_$id_cliente/";
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        // Procesar documentos con nombres únicos
        $timestamp = date('Y-m-d_H-i-s');

        $escrito_solicitud_doc_path = $uploads_dir . "escrito_solicitud_doc_{$id_cliente}_{$id_usuario}_{$timestamp}." . pathinfo($escrito_solicitud_doc['name'], PATHINFO_EXTENSION);
        move_uploaded_file($escrito_solicitud_doc['tmp_name'], $escrito_solicitud_doc_path);

        $ine_anverso_path = $uploads_dir . "ine_anverso_{$id_cliente}_{$id_usuario}_{$timestamp}." . pathinfo($ine_anverso['name'], PATHINFO_EXTENSION);
        move_uploaded_file($ine_anverso['tmp_name'], $ine_anverso_path);

        $ine_reverso_path = $uploads_dir . "ine_reverso_{$id_cliente}_{$id_usuario}_{$timestamp}." . pathinfo($ine_reverso['name'], PATHINFO_EXTENSION);
        move_uploaded_file($ine_reverso['tmp_name'], $ine_reverso_path);

        $comprobante_domicilio_path = $uploads_dir . "comprobante_domicilio_{$id_cliente}_{$id_usuario}_{$timestamp}." . pathinfo($comprobante_domicilio['name'], PATHINFO_EXTENSION);
        move_uploaded_file($comprobante_domicilio['tmp_name'], $comprobante_domicilio_path);

        $foto_lugar_path = $uploads_dir . "foto_lugar_{$id_cliente}_{$id_usuario}_{$timestamp}." . pathinfo($foto_lugar['name'], PATHINFO_EXTENSION);
        move_uploaded_file($foto_lugar['tmp_name'], $foto_lugar_path);

        $otro_doc_path = null;
        if ($otro_doc && $otro_doc['tmp_name']) {
            $otro_doc_path = $uploads_dir . "otro_doc_{$id_cliente}_{$id_usuario}_{$timestamp}." . pathinfo($otro_doc['name'], PATHINFO_EXTENSION);
            move_uploaded_file($otro_doc['tmp_name'], $otro_doc_path);
        }

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
    <title>Registrar Documentos</title>
    <link rel="stylesheet" href="rservice_doc.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
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
    <div class="form-container">
        <h1>Registrar Documentos Complementarios</h1>
        <form action="register_service_doc.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="id_cliente">ID Cliente:</label>
                <input type="text" id="id_cliente" name="id_cliente" required>
            </div>
            <div id="map" style="height: 400px; width: 100%;"></div>
            <div class="form-group">
                <label for="latitud">Latitud:</label>
                <input type="text" id="latitud" name="latitud" readonly required>
            </div>
            <div class="form-group">
                <label for="longitud">Longitud:</label>
                <input type="text" id="longitud" name="longitud" readonly required>
            </div>
            <div class="form-group">
                <label for="escrito_solicitud_doc">Escrito Solicitud:</label>
                <input type="file" id="escrito_solicitud_doc" name="escrito_solicitud_doc" required>
            </div>
            <div class="form-group">
                <label for="ine_anverso">INE Anverso:</label>
                <input type="file" id="ine_anverso" name="ine_anverso" required>
            </div>
            <div class="form-group">
                <label for="ine_reverso">INE Reverso:</label>
                <input type="file" id="ine_reverso" name="ine_reverso" required>
            </div>
            <div class="form-group">
                <label for="comprobante_domicilio">Comprobante de Domicilio:</label>
                <input type="file" id="comprobante_domicilio" name="comprobante_domicilio" required>
            </div>
            <div class="form-group">
                <label for="foto_lugar">Foto del Lugar:</label>
                <input type="file" id="foto_lugar" name="foto_lugar" required>
            </div>
            <div class="form-group">
                <label for="otro_doc">Otro Documento (Opcional):</label>
                <input type="file" id="otro_doc" name="otro_doc">
            </div>
            <div class="form-group">
                <button type="submit">Guardar Documentos</button>
            </div>
        </form>
        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
