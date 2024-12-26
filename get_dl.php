<?php
header('Content-Type: application/json');
include 'db.php'; // Archivo de conexión a la base de datos

if (isset($_GET['ID_ESTADO'])) {
    $ID_ESTADO = intval($_GET['ID_ESTADO']);

    try {
        // Consultar los distritos locales basados en el estado seleccionado
        $query = "SELECT ID_DISTRITO_LOCAL, CABECERA_DISTRITAL_LOCAL FROM distritos_locales WHERE ID_ESTADO = ? ORDER BY CABECERA_DISTRITAL_LOCAL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $ID_ESTADO);
        $stmt->execute();
        $result = $stmt->get_result();

        $distritos_locales = [];
        while ($row = $result->fetch_assoc()) {
            $distritos_locales[] = [
                'ID_DISTRITO_LOCAL' => $row['ID_DISTRITO_LOCAL'],
                'CABECERA_DISTRITAL_LOCAL' => $row['CABECERA_DISTRITAL_LOCAL']
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