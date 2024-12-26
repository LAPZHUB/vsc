<?php
header('Content-Type: application/json');
include 'db.php'; // Archivo de conexión a la base de datos

if (isset($_GET['estado_id'])) {
    $estado_id = intval($_GET['estado_id']);

    try {
        // Consultar los distritos locales basados en el estado seleccionado
        $query = "SELECT id, nombre FROM distritos_locales WHERE estado_id = ? ORDER BY nombre";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $estado_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $distritos_locales = [];
        while ($row = $result->fetch_assoc()) {
            $distritos_locales[] = [
                'id' => $row['id'],
                'nombre' => $row['nombre']
            ];
        }

        echo json_encode($distritos_locales);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al obtener los distritos locales: ' . $e->getMessage()]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'No se proporcionó el estado ID']);
}

$conn->close();
?>