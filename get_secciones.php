<?php
include 'db.php';

if (isset($_POST['colonia'])) {
    $colonia_id = $_POST['colonia'];
    $query = "SELECT SECCION, NOMBRE FROM secciones WHERE ID_COLONIA = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $colonia_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>Seleccione una sección</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['SECCION'] . "'>" . $row['NOMBRE'] . "</option>";
    }
    $stmt->close();
}
?>