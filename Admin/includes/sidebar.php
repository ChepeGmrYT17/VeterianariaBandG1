<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$actual = basename($_SERVER['PHP_SELF'], '.php');

$usuarioNombre = $_SESSION['usuario_nombre'] ?? 'Invitado';
$usuarioRol    = $_SESSION['usuario_rol'] ?? 'Sin rol';
$modulosPermitidos = function_exists('obtenerModulosPermitidos')
    ? obtenerModulosPermitidos($usuarioRol)
    : ['index', 'expedientes', 'citas', 'inventario', 'tienda', 'calendario', 'usuarios', 'settings'];

$menu = [
    'index'       => ['Inicio',      'fa-house',             true],
    'expedientes' => ['Expedientes', 'fa-folder',            false],
    'citas'       => ['Citas',       'fa-calendar-check',    false],
    'inventario'  => ['Inventario',  'fa-boxes-stacked',     false],
    'tienda'      => ['Tienda',      'fa-store',             false],
    'calendario'  => ['Calendario',  'fa-calendar',          false],
];
?>

<aside class="barra-lateral offcanvas-lg offcanvas-start" tabindex="-1" id="barraLateral">
    <button type="button" id="botonColapsar" class="boton-colapsar d-none d-lg-flex" title="Colapsar / Expandir menú">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <div class="offcanvas-body">
        <!-- Logo -->
        <div class="logo-barra">
            <img src="../assets/img/Logo_b&g.png" alt="Logo" width="40">
            <span class="etiqueta">B&amp;G Animal Care</span>
            <button type="button" class="boton-cerrar d-lg-none ms-auto"
                data-bs-dismiss="offcanvas" data-bs-target="#barraLateral"
                aria-label="Cerrar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Menú filtrado según el rol del usuario -->
        <nav class="menu-barra">
            <?php foreach ($menu as $archivo => [$etiqueta, $icono, $tieneAviso]): ?>
                <?php if (in_array($archivo, $modulosPermitidos, true)): ?>
                    <a href="<?= $archivo ?>.php" class="item-nav <?= $actual === $archivo ? 'activo' : '' ?>"
                       title="<?= htmlspecialchars($etiqueta) ?>">
                        <i class="fa-solid <?= $icono ?>"></i>
                        <span class="etiqueta"><?= htmlspecialchars($etiqueta) ?></span>
                        <?php if ($tieneAviso): ?>
                            <span class="punto-aviso"></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <!-- Zona inferior: Gestión de Usuarios (solo Admin), Usuario activo y Cerrar Sesión -->
        <div class="parte-baja">
            <?php if (in_array('usuarios', $modulosPermitidos, true)): ?>
                <a href="usuarios.php" class="item-nav <?= $actual === 'usuarios' ? 'activo' : '' ?>" title="Usuarios y Roles">
                    <i class="fa-solid fa-users-gear"></i>
                    <span class="etiqueta">Usuarios</span>
                </a>
            <?php endif; ?>

            <!-- Tarjeta con quién inició sesión -->
            <div class="perfil-usuario-caja" title="<?= htmlspecialchars($usuarioNombre . ' (' . $usuarioRol . ')') ?>">
                <span class="avatar">
                    <i class="fa-solid fa-user-doctor" style="color: var(--color-activo-texto);"></i>
                    <span class="indicador-conectado" title="En línea"></span>
                </span>
                <div class="perfil-info etiqueta">
                    <span class="perfil-nombre"><?= htmlspecialchars($usuarioNombre) ?></span>
                    <span class="perfil-rol"><?= htmlspecialchars($usuarioRol) ?></span>
                </div>
            </div>

            <!-- Botón Cerrar Sesión -->
            <a href="../logout.php" class="btn-cerrar-sesion" title="Cerrar sesión">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="etiqueta">Cerrar sesión</span>
            </a>
        </div>
    </div>
</aside>
