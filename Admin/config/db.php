<?php
/**
 * Conexión a Base de Datos (PDO) e inicialización de tablas Multi-Usuario
 * Veterinaria B&G Animal Care
 */

$DB_HOST = 'localhost';
$DB_NAME = 'edgarge2_animalcare';     // <-- Cambia por el nombre de tu BD en cPanel
$DB_USER = 'edgarge2_animalcare';               // <-- Cambia por tu usuario MySQL en cPanel
$DB_PASS = '#}{ZWMpprdhuR!dF';                   // <-- Cambia por tu contraseña MySQL en cPanel

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión con la base de datos. Verifica tus credenciales en Admin/config/db.php.");
}

/**
 * Crea las tablas de roles y usuarios si aún no existen y carga usuarios iniciales
 */
function inicializarUsuariosDemo(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(50) NOT NULL UNIQUE,
            descripcion VARCHAR(255)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            rol_id INT NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            activo TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (rol_id) REFERENCES roles(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Insertar roles base si la tabla está vacía
    $totalRoles = (int) $pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    if ($totalRoles === 0) {
        $pdo->exec("
            INSERT INTO roles (id, nombre, descripcion) VALUES
            (1, 'Administrador', 'Acceso total a todos los módulos, finanzas, RRHH y gestión de usuarios'),
            (2, 'Veterinario', 'Acceso a expedientes clínicos, citas, calendario e inventario'),
            (3, 'Recepcionista', 'Acceso a agenda de citas, calendario y ventas de tienda/farmacia')
        ");
    }

    // Insertar usuarios de prueba si no hay ninguno registrado
    $totalUsuarios = (int) $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    if ($totalUsuarios === 0) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (rol_id, nombre, email, password, activo) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([1, 'Dr. Vladimir Bucio', 'admin@bgvet.mx', password_hash('Admin123*', PASSWORD_DEFAULT)]);
        $stmt->execute([2, 'Dra. Sofía Méndez', 'veterinario@bgvet.mx', password_hash('Vet123*', PASSWORD_DEFAULT)]);
        $stmt->execute([3, 'Ana Torres', 'recepcion@bgvet.mx', password_hash('Recep123*', PASSWORD_DEFAULT)]);
    }
}

inicializarUsuariosDemo($pdo);
