<?php
include 'db.php'; // Conexión a la base de datos

if (isset($_POST['ID_ESTADO'])) {
    $ID_ESTADO = intval($_POST['ID_ESTADO']);
    $query = "SELECT ID_DISTRITO_FEDERAL, CABECERA_DISTRITAL_FEDERAL 
              FROM distritos_federales 
              WHERE ID_ESTADO = $ID_ESTADO 
              ORDER BY CABECERA_DISTRITAL_FEDERAL";
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

$conn->close();
?>
