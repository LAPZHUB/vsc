<?php
// db.php: Conexión a la base de datos
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// auth.php: Login de usuarios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php';
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $conn->prepare("SELECT * FROM dirigente WHERE usuario = :username");
    $query->bindParam(':username', $username);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id_dirigente'];
        echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
    }
    exit;
}

// dirigente.php: CRUD para "dirigente"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nom_dirigente'])) {
    include 'db.php';

    $nombre = $_POST['nom_dirigente'];
    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = $conn->prepare("INSERT INTO dirigente (nom_dirigente, usuario, password) VALUES (:nombre, :usuario, :password)");
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':usuario', $usuario);
    $query->bindParam(':password', $password);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Dirigente creado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear dirigente']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['id_lider'])) {
    include 'db.php';

    $query = $conn->query("SELECT * FROM dirigente");
    $dirigentes = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($dirigentes);
    exit;
}

// lider.php: CRUD para "lider"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nom_lider'])) {
    include 'db.php';

    $id_dirigente = $_POST['id_dirigente'];
    $nombre = $_POST['nom_lider'];
    $usuario = $_POST['usuario'];
    $estado = $_POST['estado_lider'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = $conn->prepare("INSERT INTO lider (id_dirigente, nom_lider, usuario, estado_lider, password) VALUES (:id_dirigente, :nombre, :usuario, :estado, :password)");
    $query->bindParam(':id_dirigente', $id_dirigente);
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':usuario', $usuario);
    $query->bindParam(':estado', $estado);
    $query->bindParam(':password', $password);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Líder creado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear líder']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_dirigente'])) {
    include 'db.php';

    $id_dirigente = $_GET['id_dirigente'];
    $query = $conn->prepare("SELECT * FROM lider WHERE id_dirigente = :id_dirigente");
    $query->bindParam(':id_dirigente', $id_dirigente);
    $query->execute();
    $lideres = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($lideres);
    exit;
}

// capturista.php: CRUD para "capturista"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nom_capturista'])) {
    include 'db.php';

    $id_dirigente = $_POST['id_dirigente'];
    $id_lider = $_POST['id_lider'];
    $nombre = $_POST['nom_capturista'];
    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = $conn->prepare("INSERT INTO capturista (id_dirigente, id_lider, nom_capturista, usuario, password) VALUES (:id_dirigente, :id_lider, :nombre, :usuario, :password)");
    $query->bindParam(':id_dirigente', $id_dirigente);
    $query->bindParam(':id_lider', $id_lider);
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':usuario', $usuario);
    $query->bindParam(':password', $password);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Capturista creado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear capturista']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_lider'])) {
    include 'db.php';

    $id_lider = $_GET['id_lider'];
    $query = $conn->prepare("SELECT * FROM capturista WHERE id_lider = :id_lider");
    $query->bindParam(':id_lider', $id_lider);
    $query->execute();
    $capturistas = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($capturistas);
    exit;
}
?>