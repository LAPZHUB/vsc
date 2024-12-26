<?php
include 'db.php'; // Conexión a la base de datos

if (isset($_POST['ID_ESTADO'])) {
    $ID_ESTADO = intval($_POST['ID_ESTADO']);
    $query = "SELECT ID_MUNICIPIO, NOMBRE_MUNICIPIO 
              FROM municipios 
              WHERE ID_ESTADO = $ID_ESTADO 
              ORDER BY NOMBRE_MUNICIPIO";
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

$conn->close();
?>