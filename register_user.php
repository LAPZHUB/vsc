<?php
session_start();
include 'db.php'; // Archivo para conectar a la base de datos

// Verificar si el usuario tiene el rol correcto
if ($_SESSION['role'] != 'superusuario') {
    echo "Acceso denegado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Datos del cliente
    $name = mysqli_real_escape_string($conexion, $_POST['name']);
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $phone = mysqli_real_escape_string($conexion, $_POST['phone']);
    $address = mysqli_real_escape_string($conexion, $_POST['address']);

    // Datos del usuario
    $username = mysqli_real_escape_string($conexion, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encriptar contraseña

    // Iniciar transacción para asegurar consistencia
    mysqli_begin_transaction($conexion);

    try {
        // Insertar cliente
        $query_client = "INSERT INTO clients (name, email, phone, address) VALUES ('$name', '$email', '$phone', '$address')";
        mysqli_query($conexion, $query_client);
        $id_client = mysqli_insert_id($conexion);

        // Insertar usuario asociado
        $query_user = "INSERT INTO users (username, password, role, id_client, activo) VALUES ('$username', '$password', 'cliente', $id_client, 1)";
        mysqli_query($conexion, $query_user);

        // Confirmar transacción
        mysqli_commit($conexion);
        echo "Cliente y usuario creados exitosamente.";
    } catch (Exception $e) {
        // Revertir cambios en caso de error
        mysqli_rollback($conexion);
        echo "Error al registrar cliente y usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cliente</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Registrar Servicio</h2>
        <form action="registro_servicio.php" method="POST">
            <h4>Datos del Servicio</h4>
            <div class="mb-3">
                <label for="direccion_general" class="form-label">Dirección General</label>
                <input type="text" class="form-control" id="direccion_general" name="direccion_general" required>
            </div>
            <div class="mb-3">
                <label for="area_responsable" class="form-label">Área Responsable</label>
                <input type="text" class="form-control" id="area_responsable" name="area_responsable" required>
            </div>
            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select" required>
                    <option value="">Seleccione un estado</option>
                    <?php
                    $query_estados = "SELECT id, nombre FROM estados ORDER BY nombre ASC";
                    $result_estados = mysqli_query($conexion, $query_estados);
                    while ($row = mysqli_fetch_assoc($result_estados)) {
                        echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="municipio" class="form-label">Municipio</label>
                <select id="municipio" name="municipio" class="form-select" required>
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
            <button type="submit" class="btn btn-primary">Registrar Servicio</button>
        </form>
    </div>
    <!-- Scripts para cargar datos dinámicamente -->
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

        document.getElementById('colonia').addEventListener('change', function () {
            const coloniaId = this.value;
            const seccionSelect = document.getElementById('seccion');

            seccionSelect.innerHTML = '<option value="">Seleccione una sección</option>';

            if (coloniaId) {
                fetch(`get_secciones.php?colonia_id=${coloniaId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(seccion => {
                            const option = document.createElement('option');
                            option.value = seccion.id;
                            option.textContent = seccion.nombre;
                            seccionSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

