<?php
require_once '../includes/auth.php';
verificarSesion();
requerirRol(['Administrador', 'Veterinario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario y Almacén | B&amp;G Animal Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../img/logo.png">
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
            <button id="botonMenuMovil" class="boton-menu-movil mb-3 d-lg-none" aria-label="Menú">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="bg-white rounded-4 p-4 shadow-sm border-0">
                <span class="badge rounded-pill px-3 py-1 mb-2" style="background: var(--color-activo-fondo); color: var(--color-activo-texto);">
                    En desarrollo
                </span>
                <h1 class="h4 fw-bold mb-2" style="color: var(--color-activo-texto);">
                    <i class="fa-solid fa-boxes-stacked me-2"></i>Inventario y Almacén
                </h1>
                <p class="text-muted mb-3">Módulo de Fase 2: Control de medicamentos, vacunas, consumibles, stock mínimo y semáforo de caducidades.</p>
                <a href="citas.php" class="btn text-white rounded-pill px-4 fw-semibold" style="background: var(--color-activo-texto);">
                    Ir a Agenda de Citas
                </a>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/sidebar.js"></script>
</body>
</html>
