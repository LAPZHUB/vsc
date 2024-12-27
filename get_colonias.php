<?php
include 'db.php';

if (isset($_POST['$ID_MUNICIPIO'])) {
    $colonia_id = $_POST['colonia'];
    $query = "SELECT CATEGORIA, NOMBRE_COLONIA 
                FROM catalogocolonia_2024 
                WHERE ID_MUNICIPIO = $MUNICIPIO
                ORDER BY NOMBRE_COLONIA ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $NOMBRE_COLONIA);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>Seleccione una colonia</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['CATEGORIA'] . "'>" . $row['NOMBRE_COLONIA'] . "</option>";
    }
    $stmt->close();
}
?>
