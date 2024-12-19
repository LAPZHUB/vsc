<?php
include 'db.php';

if (isset($_POST['municipio'])) {
    $municipio_id = $_POST['municipio'];
    $query = "SELECT ID, NOMBRE FROM catalogocolonia_2024 WHERE ID_MUNICIPIO = ?";
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
