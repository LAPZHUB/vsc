<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'superusuario') {
    header('Location: login.html');
    exit();
}

// Procesar el formulario al ser enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $estado = $_POST['estado'];
    $municipio = $_POST['municipio'];
    $distrito_federal = $_POST['distrito_federal'];
    $distrito_local = $_POST['distrito_local'];

    // Preparar la consulta para insertar el nuevo cliente
    $stmt = $conn->prepare("INSERT INTO clients (company_name, contact_person, phone, address, estado_id, municipio_id, distrito_federal_id, distrito_local_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiiiii", $company_name, $contact_person, $phone, $address, $estado, $municipio, $distrito_federal, $distrito_local, $_SESSION['user_id']);

    if ($stmt->execute()) {
        header("Location: index_clients.php?msg=Nuevo cliente registrado exitosamente");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="rclient.css">
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    
    <title>Registrar Cliente</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(0deg, rgb(1, 32, 56) 16%, rgb(30, 77, 105) 89%);
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
                }
            });

            // Cargar municipios al seleccionar un estado
            $('#estado').change(function () {
                const estadoId = $(this).val();
                if (estadoId) {
                    $.ajax({
                        url: 'ajax.php',
                        method: 'POST',
                        data: { action: 'get_municipios', ID_ESTADO: estadoId },
                        success: function (data) {
                            $('#municipio').html('<option value="">Seleccione un municipio</option>' + data);
                        }
                    });
                }
            });

            // Cargar distritos federales al seleccionar un municipio
            $('#municipio').change(function () {
                const municipioId = $(this).val();
                if (municipioId) {
                    $.ajax({
                        url: 'ajax.php',
                        method: 'POST',
                        data: { action: 'get_distritos_federales', municipio_id: municipioId },
                        success: function (data) {
                            $('#distrito_federal').html('<option value="">Seleccione un distrito federal</option>' + data);
                        }
                    });
                }
            });

            // Cargar distritos locales al seleccionar un municipio
            $('#municipio').change(function () {
                const municipioId = $(this).val();
                if (municipioId) {
                    $.ajax({
                        url: 'ajax.php',
                        method: 'POST',
                        data: { action: 'get_distritos_locales', municipio_id: municipioId },
                        success: function (data) {
                            $('#distrito_local').html('<option value="">Seleccione un distrito local</option>' + data);
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>
    <div class="container py-4 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card" style="border-radius: 1rem; border: none">
                    <div class="row g-0">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img src="img/skyscrapers4.jpg" alt="Formulario de registro" class="img-fluid" style="border-radius: 1rem 0 0 1rem;"/>
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">
                                <form action="register_client.php" method="POST">
                                    <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Registrar Nuevo Cliente</h5>

                                    <div class="form-group mb-3">
                                        <label for="company_name">Nombre de la Empresa:</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="contact_person">Persona de Contacto:</label>
                                        <input type="text" id="contact_person" name="contact_person" class="form-control" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="phone">Teléfono:</label>
                                        <input type="tel" id="phone" name="phone" class="form-control" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="address">Dirección:</label>
                                        <textarea id="address" name="address" class="form-control" required></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="estado">Estado:</label>
                                        <select id="estado" name="estado" class="form-control" required>
                                            <option value="">Seleccione un estado</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="municipio">Municipio:</label>
                                        <select id="municipio" name="municipio" class="form-control" required>
                                            <option value="">Seleccione un municipio</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="distrito_federal">Distrito Federal:</label>
                                        <select id="distrito_federal" name="distrito_federal" class="form-control" required>
                                            <option value="">Seleccione un distrito federal</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="distrito_local">Distrito Local:</label>
                                        <select id="distrito_local" name="distrito_local" class="form-control" required>
                                            <option value="">Seleccione un distrito local</option>
                                        </select>
                                    </div>

                                    <div class="pt-1 mb-4">
                                        <button class="btn btn-dark btn-lg btn-block" type="submit" name="submit">Registrar Cliente</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
