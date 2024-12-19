<?php
include 'conexion.php';
$municipio_id = $_GET['municipio_id'];
$query = "SELECT id, nombre FROM colonias WHERE municipio_id = $municipio_id";
$result = mysqli_query($conexion, $query);

$colonias = [];
while ($row = mysqli_fetch_assoc($result)) {
    $colonias[] = $row;
}
echo json_encode($colonias);
?>
