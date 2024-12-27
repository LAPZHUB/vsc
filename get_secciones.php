<?php
include 'db.php';

if (isset($_POST['ID_MUNICIPIO'])) {
    $ID_MUNICIPIO = $_POST['ID_MUNICIPIO'];
    $query = "SELECT SECCION 
    FROM secciones 
    WHERE ID_MUNICIPIO= $ID_MUNICIPIO";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ID_MUNICIPIO);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>Seleccione una sección</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['SECCION'] . "'>" . $row['NOMBRE'] . "</option>";
    }
    $stmt->close();
}
?>