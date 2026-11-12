<?php
/**
 * Middleware de Autenticación y Control de Acceso por Roles (Multi-Usuario)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

/**
 * Verifica que exista una sesión activa y que el usuario siga activo en la BD.
 * Si no, redirige al login.
 */
function verificarSesion(string $rutaLogin = '../login.php'): void {
    if (empty($_SESSION['usuario_id'])) {
        header("Location: {$rutaLogin}");
        exit;
    }

    global $pdo;
    $stmt = $pdo->prepare("
        SELECT u.id, u.nombre, u.email, u.activo, r.nombre AS rol
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch();

    if (!$usuario || (int)$usuario['activo'] !== 1) {
        session_unset();
        session_destroy();
        header("Location: {$rutaLogin}?error=inactivo");
        exit;
    }

    // Mantener datos de sesión sincronizados
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email']  = $usuario['email'];
    $_SESSION['usuario_rol']    = $usuario['rol'];
}

/**
 * Define qué módulos puede ver cada rol
 */
function obtenerModulosPermitidos(string $rol): array {
    $permisos = [
        'Administrador' => ['index', 'expedientes', 'citas', 'inventario', 'tienda', 'calendario', 'usuarios', 'settings'],
        'Veterinario'   => ['index', 'expedientes', 'citas', 'inventario', 'calendario'],
        'Recepcionista' => ['index', 'citas', 'tienda', 'calendario'],
    ];

    return $permisos[$rol] ?? ['index'];
}

/**
 * Verifica si el usuario actual tiene acceso al módulo indicado
 */
function puedeAccederModulo(string $modulo): bool {
    $rol = $_SESSION['usuario_rol'] ?? '';
    return in_array($modulo, obtenerModulosPermitidos($rol), true);
}

/**
 * Bloquea el acceso si el rol actual no está en la lista permitida
 */
function requerirRol(array $rolesPermitidos, string $redireccion = 'index.php'): void {
    $rolActual = $_SESSION['usuario_rol'] ?? '';
    if (!in_array($rolActual, $rolesPermitidos, true)) {
        header("Location: {$redireccion}?acceso_denegado=1");
        exit;
    }
}
