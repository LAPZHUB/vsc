<?php
include 'db.php'; // Conexión a la base de datos

if (isset($_POST['ID_ESTADO'])) {
    $ID_ESTADO = intval($_POST['ID_ESTADO']);
    $query = "SELECT ID_DISTIRTO_FEDERAL, CABECERA_DISTIRTAL_FEDERAL 
              FROM distritos_federales 
              WHERE ID_ESTADO = $ID_ESTADO 
              ORDER BY CABECERA_DISTIRTAL_FEDERAL";
    $result = $conn->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['ID_DISTIRTO_FEDERAL'] . '">' . $row['CABECERA_DISTIRTAL_FEDERAL'] . '</option>';
        }
    } else {
        echo '<option value="">Error al cargar distritos federales</option>';
    }
} else {
    echo '<option value="">Estado no especificado</option>';
}

$conn->close();
?>
