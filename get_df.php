<?php
header('Content-Type: application/json');
include 'db.php'; // Archivo de conexión a la base de datos

if (isset($_GET['estado_id'])) {
    $estado_id = intval($_GET['estado_id']);

    try {
        // Consultar los distritos federales basados en el estado seleccionado
        $query = "SELECT id, nombre FROM distritos_federales WHERE estado_id = ? ORDER BY nombre";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $estado_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $distritos_federales = [];
        while ($row = $result->fetch_assoc()) {
            $distritos_federales[] = [
                'id' => $row['id'],
                'nombre' => $row['nombre']
            ];
        }

        echo json_encode($distritos_federales);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al obtener los distritos federales: ' . $e->getMessage()]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'No se proporcionó el estado ID']);
}

$conn->close();
?>