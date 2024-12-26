<?php
header('Content-Type: application/json');
include 'db.php'; // Archivo de conexión a la base de datos

try {
    // Consultar los estados desde la base de datos
    $query = "SELECT id, nombre FROM estados ORDER BY nombre";
    $result = $conn->query($query);

    $estados = [];
    while ($row = $result->fetch_assoc()) {
        $estados[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre']
        ];
    }

    echo json_encode($estados);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener los estados: ' . $e->getMessage()]);
}

$conn->close();
?>