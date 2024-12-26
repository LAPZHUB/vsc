<?php
header('Content-Type: text/plain');

// Incluir conexión a la base de datos
include 'db.php';

// Array para almacenar resultados de las pruebas
$results = [];

/**
 * Test 1: Conexión a la base de datos
 */
$results['db_connection'] = $conn->connect_error ? "Error: {$conn->connect_error}" : "Conexión exitosa a la base de datos";

/**
 * Test 2: Verificar estados desde get_estado.php
 */
try {
    $query = "SELECT ID_ESTADO, NOMBRE_ESTADO FROM estados LIMIT 1";
    $result = $conn->query($query);
    $results['get_estado'] = $result && $result->num_rows > 0 ? "Estados disponibles" : "Error: No se encontraron estados";
} catch (Exception $e) {
    $results['get_estado'] = "Error al consultar estados: {$e->getMessage()}";
}

/**
 * Test 3: Verificar distritos federales desde get_df.php
 */
try {
    $query = "SELECT ID_DISTRITO_FEDERAL, CABECERA_DISTRITAL_FEDERAL FROM distritos_federales LIMIT 1";
    $result = $conn->query($query);
    $results['get_df'] = $result && $result->num_rows > 0 ? "Distritos federales disponibles" : "Error: No se encontraron distritos federales";
} catch (Exception $e) {
    $results['get_df'] = "Error al consultar distritos federales: {$e->getMessage()}";
}

/**
 * Test 4: Verificar distritos locales desde get_dl.php
 */
try {
    $query = "SELECT ID_DISTRITO_LOCAL, CABECERA_DISTRITAL_LOCAL FROM distritos_locales LIMIT 1";
    $result = $conn->query($query);
    $results['get_dl'] = $result && $result->num_rows > 0 ? "Distritos locales disponibles" : "Error: No se encontraron distritos locales";
} catch (Exception $e) {
    $results['get_dl'] = "Error al consultar distritos locales: {$e->getMessage()}";
}

/**
 * Test 5: Verificar funcionalidad de usuarios (register_user.php)
 */
try {
    $query = "SELECT id FROM users LIMIT 1";
    $result = $conn->query($query);
    $results['register_user'] = $result && $result->num_rows > 0 ? "Usuarios disponibles" : "Error: No se encontraron usuarios registrados";
} catch (Exception $e) {
    $results['register_user'] = "Error al consultar usuarios: {$e->getMessage()}";
}

/**
 * Test 6: Verificar funcionalidad de clientes (register_client.php)
 */
try {
    $query = "SELECT id FROM clients LIMIT 1";
    $result = $conn->query($query);
    $results['register_client'] = $result && $result->num_rows > 0 ? "Clientes disponibles" : "Error: No se encontraron clientes registrados";
} catch (Exception $e) {
    $results['register_client'] = "Error al consultar clientes: {$e->getMessage()}";
}

/**
 * Test 7: Verificar funcionalidad de servicios (register_service.php)
 */
try {
    $query = "SELECT id FROM servicios LIMIT 1";
    $result = $conn->query($query);
    $results['register_service'] = $result && $result->num_rows > 0 ? "Servicios disponibles" : "Error: No se encontraron servicios registrados";
} catch (Exception $e) {
    $results['register_service'] = "Error al consultar servicios: {$e->getMessage()}";
}

/**
 * Test 8: Verificar funcionalidad de documentos de servicios (register_service_doc.php)
 */
try {
    $query = "SELECT id FROM servicios WHERE escrito_solicitud_doc IS NOT NULL LIMIT 1";
    $result = $conn->query($query);
    $results['register_service_doc'] = $result && $result->num_rows > 0 ? "Documentos de servicios disponibles" : "Error: No se encontraron documentos de servicios";
} catch (Exception $e) {
    $results['register_service_doc'] = "Error al consultar documentos de servicios: {$e->getMessage()}";
}

/**
 * Imprimir resultados
 */
echo "Resultados de las pruebas:\n\n";
foreach ($results as $test => $result) {
    echo strtoupper($test) . ": $result\n";
}

$conn->close();
?>