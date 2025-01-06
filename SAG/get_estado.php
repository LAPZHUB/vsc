<?php
include 'db.php'; // Conexión a la base de datos

$query = "SELECT ID_ESTADO, NOMBRE_ESTADO FROM estados ORDER BY NOMBRE_ESTADO";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['ID_ESTADO'] . '">' . $row['NOMBRE_ESTADO'] . '</option>';
    }
} else {
    echo '<option value="">Error al cargar estados</option>';
}

$conn->close();
?>
