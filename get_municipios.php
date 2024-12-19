<?php
include 'db.php'; // Conexión a la base de datos

if (isset($_POST['estado'])) {
    $estado_id = $_POST['estado'];
    $query = "SELECT ID_MUNICIPIO, NOMBRE_MUNICIPIO FROM municipios WHERE ID_ESTADO = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $estado_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>Seleccione un municipio</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['ID_MUNICIPIO'] . "'>" . $row['NOMBRE_MUNICIPIO'] . "</option>";
    }
    $stmt->close();
}
?>