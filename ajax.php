<?php
include 'db.php'; // Asegúrate de que este archivo configure correctamente la conexión a la base de datos

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'get_estado':
            // Obtener lista de estados
            $query = "SELECT ID_ESTADO, NOMBRE_ESTADO FROM estados ORDER BY NOMBRE_ESTADO";
            $result = $conn->query($query);

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['ID_ESTADO'] . '">' . $row['NOMBRE_ESTADO'] . '</option>';
                }
            } else {
                echo '<option value="">Error al cargar estados</option>';
            }
            break;

        case 'get_df':
            // Obtener lista de distritos federales
            if (isset($_POST['ID_ESTADO'])) {
                $estado_id = intval($_POST['ID_ESTADO']);
                $query = "SELECT ID_DISTRITO_FEDERAL, CABECERA_DISTRITAL_FEDERAL FROM ID_DISTRITO_FEDERAL WHERE ID_ESTADO = $estado_id ORDER BY CABECERA_DISTRITAL_FEDERAL";
                $result = $conn->query($query);

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['ID_DISTRITO_FEDERAL'] . '">' . $row['CABECERA_DISTRITAL_FEDERAL'] . '</option>';
                    }
                } else {
                    echo '<option value="">Error al cargar distritos federales</option>';
                }
            } else {
                echo '<option value="">Estado no especificado</option>';
            }
            break;

        case 'get_dl':
            // Obtener lista de distritos locales
            if (isset($_POST['ID_ESTADO'])) {
                $estado_id = intval($_POST['ID_ESTADO']);
                $query = "SELECT ID_DISTRITO_LOCAL, CABECERA_DISTRITAL_LOCAL FROM distritos_locales WHERE ID_ESTADO = $estado_id ORDER BY CABECERA_DISTRITAL_LOCAL";
                $result = $conn->query($query);

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['ID_DISTRITO_LOCAL'] . '">' . $row['CABECERA_DISTRITAL_LOCAL'] . '</option>';
                    }
                } else {
                    echo '<option value="">Error al cargar distritos locales</option>';
                }
            } else {
                echo '<option value="">Estado no especificado</option>';
            }
            break;

        case 'get_municipios':
            // Obtener lista de municipios
            if (isset($_POST['ID_ESTADO'])) {
                $estado_id = intval($_POST['ID_ESTADO']);
                $query = "SELECT ID_ESTADO, ID_MUNICIPIO, NOMBRE_MUNICIPIO, FROM municipios WHERE ID_ESTADO = $estado_id ORDER BY NOMBRE_MUNICIPIO";
                $result = $conn->query($query);

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['ID_MUNICIPIO'] . '">' . $row['NOMBRE_MUNICIPIO'] . '</option>';
                    }
                } else {
                    echo '<option value="">Error al cargar municipios</option>';
                }
            } else {
                echo '<option value="">Estado no especificado</option>';
            }
            break;

        default:
            echo '<option value="">Acción no válida</option>';
            break;
    }
} else {
    echo '<option value="">Acción no especificada</option>';
}

$conn->close();
?>
