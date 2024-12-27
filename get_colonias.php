<?php
include 'db.php';

if (isset($_POST['$ID_ESTADO'])) {
    $municipio_id = $_POST['municipio'];
    $query = "SELECT ID_MUNICIPIO, NOMBRE_MUNICIPIO 
                FROM catalogocolonia_2024 
                WHERE ID_ESTADO = $ID_ESTADO
                ORDER BY NOMBRE_COLONIA ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $municipio_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>Seleccione una colonia</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['ID'] . "'>" . $row['NOMBRE'] . "</option>";
    }
    $stmt->close();
}
?>
