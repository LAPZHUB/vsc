<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado y tenga el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $calle = $_POST['calle'];
    $num_interior = $_POST['num_interior'];
    $num_exterior = $_POST['num_exterior'];
    $colonia = $_POST['colonia'];
    $codigo_postal = $_POST['codigo_postal'];
    $geolocalizacion = $_POST['geolocalizacion'];

    // Subida de archivos
    $escrito_solicitud_doc = $_FILES['escrito_solicitud_doc']['name'];
    $ine_anverso = $_FILES['ine_anverso']['name'];
    $ine_reverso = $_FILES['ine_reverso']['name'];
    $foto_lugar = $_FILES['foto_lugar']['name'];
    $comprobante_domicilio = $_FILES['comprobante_domicilio']['name'];
    $otro_doc = $_FILES['otro_doc']['name'];

    $upload_dir = 'uploads/';
    move_uploaded_file($_FILES['escrito_solicitud_doc']['tmp_name'], $upload_dir.$escrito_solicitud_doc);
    move_uploaded_file($_FILES['ine_anverso']['tmp_name'], $upload_dir.$ine_anverso);
    move_uploaded_file($_FILES['ine_reverso']['tmp_name'], $upload_dir.$ine_reverso);
    move_uploaded_file($_FILES['foto_lugar']['tmp_name'], $upload_dir.$foto_lugar);
    move_uploaded_file($_FILES['comprobante_domicilio']['tmp_name'], $upload_dir.$comprobante_domicilio);
    move_uploaded_file($_FILES['otro_doc']['tmp_name'], $upload_dir.$otro_doc);

    // Preparar la consulta para actualizar el servicio con los documentos subidos
    $stmt = $conn->prepare("UPDATE servicios SET escrito_solicitud_doc = ?, ine_anverso = ?, ine_reverso = ?, foto_lugar = ?, comprobante_domicilio = ?, otro_doc = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $escrito_solicitud_doc, $ine_anverso, $ine_reverso, $foto_lugar, $comprobante_domicilio, $otro_doc, $id_servicio);

    if ($stmt->execute()) {
        $message = "Documentos subidos correctamente.";
    } else {
        $message = "Error al subir los documentos: " . $stmt->error;
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
    <title>Subir Documentos del Servicio</title>
    <link rel="stylesheet" href="rservice_doc.css">
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
    <!-- API de Google Maps -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARV6H4a893nq_zdAVM0nIMv00uCoY-RDQ&libraries=places"></script>
</head>
<body>
    <header>
        <h1>Subir Documentos del Servicio</h1>
    </header>
    <div class="form-container">
        <form action="register_service_doc.php" method="POST" enctype="multipart/form-data" onsubmit="return showModal(this)">
            <label for="calle">Calle:</label>
            <input type="text" id="calle" name="calle" required>

            <label for="num_interior">Número Interior:</label>
            <input type="text" id="num_interior" name="num_interior">

            <label for="num_exterior">Número Exterior:</label>
            <input type="text" id="num_exterior" name="num_exterior" required>

            <label for="colonia">Colonia:</label>
            <input type="text" id="colonia" name="colonia" required>

            <label for="codigo_postal">Código Postal:</label>
            <input type="text" id="codigo_postal" name="codigo_postal" required>
            
            <label for="geolocalizacion">Geolocalización:</label>
            <input type="text" id="geolocalizacion" name="geolocalizacion" readonly>
            <div id="map" style="height: 300px; width: 100%;"></div>
            
            <script>
                var map, marker, geocoder;

                function initMap() {
                    geocoder = new google.maps.Geocoder();
                    map = new google.maps.Map(document.getElementById('map'), {
                        center: {lat: 19.432608, lng: -99.133209}, // Coordenadas iniciales (Ciudad de México)
                        zoom: 12
                    });

                    marker = new google.maps.Marker({
                        map: map,
                        draggable: true,
                        position: {lat: 19.432608, lng: -99.133209} // Coordenadas iniciales (Ciudad de México)
                    });

                    map.addListener('click', function(event) {
                        placeMarker(event.latLng);
                    });

                    marker.addListener('dragend', function(event) {
                        document.getElementById('geolocalizacion').value = marker.getPosition().toUrlValue();
                    });
                }

                function placeMarker(location) {
                    marker.setPosition(location);
                    map.setCenter(location);
                    document.getElementById('geolocalizacion').value = location.toUrlValue();
                }

                function codeAddress() {
                    var address = document.getElementById('calle').value + ' ' +
                                  document.getElementById('num_exterior').value + ', ' +
                                  document.getElementById('colonia').value + ', ' +
                                  document.getElementById('codigo_postal').value;
                    geocoder.geocode({ 'address': address}, function(results, status) {
                        if (status == 'OK') {
                            map.setCenter(results[0].geometry.location);
                            marker.setPosition(results[0].geometry.location);
                            document.getElementById('geolocalizacion').value = results[0].geometry.location.toUrlValue();
                        } else {
                            alert('Geocode no tuvo éxito debido a: ' + status);
                        }
                    });
                }

                document.getElementById('calle').addEventListener('blur', codeAddress);
                document.getElementById('num_exterior').addEventListener('blur', codeAddress);
                document.getElementById('colonia').addEventListener('blur', codeAddress);
                document.getElementById('codigo_postal').addEventListener('blur', codeAddress);

                window.onload = initMap;

                function goBack() { 
            window.history.back(); 
            }

            </script>

            <h3>Documentos</h3>
            <label for="escrito_solicitud_doc">Escrito de Solicitud:</label>
            <input type="file" id="escrito_solicitud_doc" name="escrito_solicitud_doc" accept=".pdf,.jpg,.jpeg,.png">
            
            <label for="ine_anverso">INE Anverso:</label>
            <input type="file" id="ine_anverso" name="ine_anverso" accept=".pdf,.jpg,.jpeg,.png">
            
            <label for="ine_reverso">INE Reverso:</label>
            <input type="file" id="ine_reverso" name="ine_reverso" accept=".pdf,.jpg,.jpeg,.png">
            
            <label for="foto_lugar">Foto del Lugar:</label>
            <input type="file" id="foto_lugar" name="foto_lugar" accept=".pdf,.jpg,.jpeg,.png">
            
            <label for="comprobante_domicilio">Comprobante de Domicilio:</label>
            <input type="file" id="comprobante_domicilio" name="comprobante_domicilio" accept=".pdf,.jpg,.jpeg,.png">
            
            <label for="otro_doc">Otro Documento:</label>
            <input type="file" id="otro_doc" name="otro_doc" accept=".pdf,.jpg,.jpeg,.png">
            
            <button type="submit">Subir Documentos</button>
            <button type="button" onclick="goBack()">Regresar</button> <!-- Botón de regresar -->