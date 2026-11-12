<?php
require_once '../includes/auth.php';
verificarSesion();

$rolActual = $_SESSION['usuario_rol'] ?? '';
$modulos = obtenerModulosPermitidos($rolActual);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal | B&amp;G Animal Care</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (iconos) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../img/logo.png">
    <!-- Nuestro CSS va DESPUÉS de Bootstrap para poder sobrescribirlo -->
    <link href="../assets/sidebar.css" rel="stylesheet">
    <script>
        if (localStorage.getItem('sidebar-colapsado') === 'true') {
            document.documentElement.classList.add('colapsada');
        }
    </script>
</head>
<body>
    <div class="layout">
        <?php include '../includes/sidebar.php'; ?>

        <main id="contenido">
            <!-- Botón hamburguesa: solo celular -->
            <button id="botonMenuMovil" class="boton-menu-movil mb-3 d-lg-none" aria-label="Menú">
                <i class="fa-solid fa-bars"></i>
            </button>

            <?php if (isset($_GET['acceso_denegado'])): ?>
                <div class="alert alert-warning rounded-4 border-0 shadow-sm mb-3">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    No tienes permisos suficientes para acceder a ese módulo con tu rol actual.
                </div>
            <?php endif; ?>

            <!-- Cabecera de Bienvenida -->
            <div class="bg-white rounded-4 p-4 shadow-sm border-0 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="badge rounded-pill px-3 py-1 mb-2" style="background: var(--color-activo-fondo); color: var(--color-activo-texto);">
                        <i class="fa-solid fa-shield-halved me-1"></i> Rol: <?= htmlspecialchars($rolActual) ?>
                    </span>
                    <h1 class="h4 fw-bold mb-1" style="color: var(--color-activo-texto);">
                        Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
                    </h1>
                    <p class="text-muted small mb-0">
                        Selecciona un módulo en el menú lateral o en los accesos directos para comenzar a gestionar la clínica veterinaria.
                    </p>
                </div>
                <a href="../logout.php" class="btn btn-outline-danger rounded-pill px-3 fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Cerrar sesión
                </a>
            </div>

            <!-- Accesos Directos según Rol -->
            <div class="row g-3">
                <?php if (in_array('citas', $modulos, true)): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="citas.php" class="text-decoration-none">
                        <div class="bg-white rounded-4 p-4 shadow-sm border-0 h-100">
                            <div class="rounded-3 d-grid mb-3" style="width:48px;height:48px;place-items:center;background:var(--color-activo-fondo);color:var(--color-activo-texto);">
                                <i class="fa-solid fa-calendar-check fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: var(--color-activo-texto);">Agenda de Citas</h5>
                            <p class="text-muted small mb-0">Visualiza el calendario, confirma o cancela citas por WhatsApp y agenda consultas.</p>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <?php if (in_array('expedientes', $modulos, true)): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="expedientes.php" class="text-decoration-none">
                        <div class="bg-white rounded-4 p-4 shadow-sm border-0 h-100">
                            <div class="rounded-3 d-grid mb-3" style="width:48px;height:48px;place-items:center;background:var(--color-activo-fondo);color:var(--color-activo-texto);">
                                <i class="fa-solid fa-folder fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: var(--color-activo-texto);">Expedientes Clínicos</h5>
                            <p class="text-muted small mb-0">Registro de tutores, pacientes, diagnósticos, cirugías y tratamientos.</p>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <?php if (in_array('inventario', $modulos, true)): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="inventario.php" class="text-decoration-none">
                        <div class="bg-white rounded-4 p-4 shadow-sm border-0 h-100">
                            <div class="rounded-3 d-grid mb-3" style="width:48px;height:48px;place-items:center;background:var(--color-activo-fondo);color:var(--color-activo-texto);">
                                <i class="fa-solid fa-boxes-stacked fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: var(--color-activo-texto);">Inventario y Almacén</h5>
                            <p class="text-muted small mb-0">Control de medicamentos, vacunas, existencias y semáforo de caducidades.</p>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <?php if (in_array('tienda', $modulos, true)): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="tienda.php" class="text-decoration-none">
                        <div class="bg-white rounded-4 p-4 shadow-sm border-0 h-100">
                            <div class="rounded-3 d-grid mb-3" style="width:48px;height:48px;place-items:center;background:var(--color-activo-fondo);color:var(--color-activo-texto);">
                                <i class="fa-solid fa-store fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: var(--color-activo-texto);">Tienda y Caja</h5>
                            <p class="text-muted small mb-0">Cobros de consultas, venta de farmacia, ingresos, egresos y cortes de caja.</p>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <?php if (in_array('usuarios', $modulos, true)): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="usuarios.php" class="text-decoration-none">
                        <div class="bg-white rounded-4 p-4 shadow-sm border-0 h-100">
                            <div class="rounded-3 d-grid mb-3" style="width:48px;height:48px;place-items:center;background:var(--color-activo-fondo);color:var(--color-activo-texto);">
                                <i class="fa-solid fa-users-gear fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: var(--color-activo-texto);">Usuarios y Roles</h5>
                            <p class="text-muted small mb-0">Administra las cuentas del personal veterinario y sus niveles de permiso.</p>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <div class="col-md-6 col-lg-4">
                    <a href="../../citas.php" target="_blank" rel="noopener" class="text-decoration-none">
                        <div class="bg-white rounded-4 p-4 shadow-sm border-0 h-100">
                            <div class="rounded-3 d-grid mb-3" style="width:48px;height:48px;place-items:center;background:#dcfce7;color:#15803d;">
                                <i class="fa-solid fa-globe fs-5"></i>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: var(--color-activo-texto);">Vista Pública de Citas</h5>
                            <p class="text-muted small mb-0">Abre la página pública donde los clientes reservan sus horarios disponibles.</p>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/sidebar.js"></script>
</body>
</html>
