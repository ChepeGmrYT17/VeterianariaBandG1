<?php
/**
 * API REST para Gestión de Citas - Veterinaria B&G Animal Care
 * Compatible con MySQL / MariaDB en HostGator
 */
header('Content-Type: application/json; charset=utf-8');

// Configura tus credenciales de HostGator (cPanel -> Manage My Databases)
$host = 'localhost';
$db   = 'edgarge2_animalcare';
$user = 'edgarge2_animalcare';
$pass = '#}{ZWMpprdhuR!dF';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

// Crear tabla automática si aún no existe
$pdo->exec("
    CREATE TABLE IF NOT EXISTS citas_web (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fecha DATE NOT NULL,
        hora VARCHAR(10) NOT NULL,
        especie VARCHAR(50) NOT NULL,
        nombre_mascota VARCHAR(100) NOT NULL,
        motivo TEXT NOT NULL,
        nombre_propietario VARCHAR(150) NOT NULL,
        telefono VARCHAR(20) NOT NULL,
        estado ENUM('Pendiente', 'Confirmada', 'Cancelada') DEFAULT 'Pendiente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$action = $_GET['action'] ?? 'listar';

if ($action === 'listar') {
    $stmt = $pdo->query("SELECT id, fecha, hora, especie, nombre_mascota, motivo, nombre_propietario, telefono, estado FROM citas_web ORDER BY fecha ASC, hora ASC");
    echo json_encode(['citas' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validar que el horario no esté ya ocupado
    $check = $pdo->prepare("SELECT COUNT(*) FROM citas_web WHERE fecha = ? AND hora = ? AND estado != 'Cancelada'");
    $check->execute([$data['fecha'], $data['hora']]);
    if ($check->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Horario no disponible']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO citas_web (fecha, hora, especie, nombre_mascota, motivo, nombre_propietario, telefono, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pendiente')
    ");
    $stmt->execute([
        $data['fecha'],
        $data['hora'],
        $data['especie'],
        $data['nombre_mascota'],
        $data['motivo'],
        $data['nombre_propietario'],
        $data['telefono']
    ]);

    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

if ($action === 'estado' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE citas_web SET estado = ? WHERE id = ?");
    $stmt->execute([$data['estado'], $data['id']]);
    echo json_encode(['ok' => true]);
    exit;
}