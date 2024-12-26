<?php
header('Content-Type: application/json');
include 'db.php'; // Archivo de conexión a la base de datos

if (isset($_GET['ID_ESTADO'])) {
    $estado_id = intval($_GET['ID_ESTADO']);

    try {
        // Consultar los distritos federales basados en el estado seleccionado
        $query = "SELECT ID_DISTRITO_FEDERAL, CABECERA_DISTRITAL_FEDERAL FROM distritos_federales WHERE ID_ESTADO = ? ORDER BY NOMBRE_ESTADO";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $ID_ESTADO);
        $stmt->execute();
        $result = $stmt->get_result();

        $distritos_federales = [];
        while ($row = $result->fetch_assoc()) {
            $distritos_federales[] = [
                'ID_DISTRITO_FEDERAL' => $row['ID_DISTRITO_FEDERAL'],
                'CABECERA_DISTRITAL_FEDERAL' => $row['CABECERA_DISTRITAL_FEDERAL']
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