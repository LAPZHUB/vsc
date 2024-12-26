<?php
include 'db.php'; // Conexión a la base de datos

if (isset($_POST['ID_ESTADO'])) {
    $ID_ESTADO = intval($_POST['ID_ESTADO']);
    $query = "SELECT ID_DISTRITO_LOCAL, CABECERA_DISTRITAL_LOCAL 
              FROM distritos_locales 
              WHERE ID_ESTADO = $ID_ESTADO 
              ORDER BY CABECERA_DISTRITAL_LOCAL";
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

$conn->close();
?>
