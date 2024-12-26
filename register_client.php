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

    // Validar que el campo 'name' no esté vacío
    if (empty($name)) {
        echo "El campo 'Nombre' es obligatorio.";
        exit();
    }

    // Otros datos
    $ID_ESTADO = isset($_POST['estado']) ? intval($_POST['estado']) : null;
    $DF = isset($_POST['distrito_federal']) ? intval($_POST['distrito_federal']) : null;
    $DL = isset($_POST['distrito_local']) ? intval($_POST['distrito_local']) : null;
    $ID_MUNICIPIO = isset($_POST['municipio']) ? intval($_POST['municipio']) : null;

    // Inserción en la base de datos
    $query = "INSERT INTO clients (name, ID_ESTADO, DF, DL, ID_MUNICIPIO) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siiii", $name, $ID_ESTADO, $DF, $DL, $ID_MUNICIPIO);

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

            <!-- Campos dependientes -->
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" required>
                <option value="">Seleccione un estado</option>
                <!-- Opciones dinámicas cargadas desde AJAX -->
                <!-- El valor será el ID_ESTADO, pero el texto mostrado será el NOMBRE_ESTADO -->
            </select>

            <label for="distrito_federal">Distrito Federal:</label>
            <select id="distrito_federal" name="distrito_federal">
                <option value="">Seleccione un distrito federal</option>
                <!-- Opciones cargadas dinámicamente -->
            </select>

            <label for="distrito_local">Distrito Local:</label>
            <select id="distrito_local" name="distrito_local">
                <option value="">Seleccione un distrito local</option>
                <!-- Opciones cargadas dinámicamente -->
            </select>

            <label for="municipio">Municipio:</label>
            <select id="municipio" name="municipio">
                <option value="">Seleccione un municipio</option>
                <!-- Opciones cargadas dinámicamente -->
            </select>

            <button type="submit">Registrar Cliente</button>
        </form>
        <button onclick="goBack()">Regresar</button> <!-- Botón de regresar -->
    </div>

    <!-- Ventana Flotante
::contentReference[oaicite:0]{index=0}