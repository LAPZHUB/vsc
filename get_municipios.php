<?php
include 'conexion.php';
$estado_id = $_GET['estado_id'];
$query = "SELECT id, nombre FROM municipios WHERE estado_id = $estado_id";
$result = mysqli_query($conexion, $query);

$municipios = [];
while ($row = mysqli_fetch_assoc($result)) {
    $municipios[] = $row;
}
echo json_encode($municipios);
?>
