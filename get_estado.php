<?php
header('Content-Type: application/json');
include 'db.php'; // Archivo de conexión a la base de datos

try {
    // Consultar los estados desde la base de datos
    $query = "SELECT ID_ESTADO, NOMBRE_ESTADO FROM estados ORDER BY NOMBRE_ESTADO ";
    $result = $conn->query($query);

    $estados = [];
    while ($row = $result->fetch_assoc()) {
        $estados[] = [
            'ID_ESTADO' => $row['ID_ESTADO'],
            'NOMBRE_ESTADO ' => $row['NOMBRE_ESTADO ']
        ];
    }

    echo json_encode($estados);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener los estados: ' . $e->getMessage()]);
}

$conn->close();
?>