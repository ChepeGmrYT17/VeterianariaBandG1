<?php
require_once '../includes/auth.php';
verificarSesion();
requerirRol(['Administrador']);

$alerta = '';
$tipoAlerta = 'success';

// Procesar acciones (Crear usuario, Cambiar estado activo/inactivo, Cambiar contraseña)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $nombre   = trim($_POST['nombre'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $rolId    = (int)($_POST['rol_id'] ?? 2);
        $password = $_POST['password'] ?? '';

        if ($nombre === '' || $email === '' || $password === '') {
            $alerta = 'Todos los campos son obligatorios.';
            $tipoAlerta = 'danger';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO usuarios (rol_id, nombre, email, password, activo) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$rolId, $nombre, $email, password_hash($password, PASSWORD_DEFAULT)]);
                $alerta = "Usuario '{$nombre}' creado correctamente.";
            } catch (PDOException $e) {
                $alerta = 'El correo electrónico ya está registrado.';
                $tipoAlerta = 'danger';
            }
        }
    } elseif ($accion === 'toggle_estado') {
        $idUsuario = (int)($_POST['usuario_id'] ?? 0);
        if ($idUsuario === (int)$_SESSION['usuario_id']) {
            $alerta = 'No puedes desactivar tu propia cuenta mientras tienes la sesión abierta.';
            $tipoAlerta = 'warning';
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET activo = IF(activo = 1, 0, 1) WHERE id = ?");
            $stmt->execute([$idUsuario]);
            $alerta = 'Estado del usuario actualizado.';
        }
    } elseif ($accion === 'cambiar_password') {
        $idUsuario = (int)($_POST['usuario_id'] ?? 0);
        $nuevaPass = $_POST['nueva_password'] ?? '';
        if ($nuevaPass !== '') {
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->execute([password_hash($nuevaPass, PASSWORD_DEFAULT), $idUsuario]);
            $alerta = 'Contraseña actualizada correctamente.';
        }
    }
}

$roles = $pdo->query("SELECT id, nombre, descripcion FROM roles ORDER BY id ASC")->fetchAll();
$usuarios = $pdo->query("
    SELECT u.id, u.nombre, u.email, u.activo, u.created_at, r.nombre AS rol
    FROM usuarios u
    INNER JOIN roles r ON u.rol_id = r.id
    ORDER BY u.id ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios | B&amp;G Animal Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
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

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h1 class="h4 fw-bold mb-1" style="color: var(--color-activo-texto);">
                        <i class="fa-solid fa-users-gear me-2"></i>Gestión Multi-Usuario y Roles
                    </h1>
                    <p class="text-muted small mb-0">Administra las cuentas del personal veterinario y sus permisos de acceso.</p>
                </div>
                <button type="button" class="btn text-white rounded-pill px-4 fw-semibold" style="background: var(--color-activo-texto);" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                    <i class="fa-solid fa-user-plus me-1"></i> Nuevo Usuario
                </button>
            </div>

            <?php if ($alerta !== ''): ?>
                <div class="alert alert-<?= $tipoAlerta ?> rounded-4 border-0 shadow-sm mb-4">
                    <?= htmlspecialchars($alerta) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-4 shadow-sm p-3 p-md-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($u['nombre']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-1" style="background: var(--color-activo-fondo); color: var(--color-activo-texto);">
                                            <?= htmlspecialchars($u['rol']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ((int)$u['activo'] === 1): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger rounded-pill">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                    onclick="abrirModalPass(<?= (int)$u['id'] ?>, '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>')">
                                                <i class="fa-solid fa-key"></i> Clave
                                            </button>

                                            <?php if ((int)$u['id'] !== (int)$_SESSION['usuario_id']): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="accion" value="toggle_estado">
                                                    <input type="hidden" name="usuario_id" value="<?= (int)$u['id'] ?>">
                                                    <button type="submit" class="btn btn-sm <?= (int)$u['activo'] === 1 ? 'btn-outline-danger' : 'btn-outline-success' ?> rounded-pill px-3">
                                                        <?= (int)$u['activo'] === 1 ? 'Desactivar' : 'Activar' ?>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Nuevo Usuario -->
    <div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <h5 class="modal-title fw-bold" style="color: var(--color-activo-texto);">Registrar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" class="modal-body p-4">
                    <input type="hidden" name="accion" value="crear">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre completo *</label>
                        <input type="text" name="nombre" class="form-control rounded-3" required placeholder="Ej. Dr. Juan Ramírez">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Correo electrónico *</label>
                        <input type="email" name="email" class="form-control rounded-3" required placeholder="usuario@bgvet.mx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Rol / Nivel de acceso *</label>
                        <select name="rol_id" class="form-select rounded-3" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= (int)$r['id'] ?>">
                                    <?= htmlspecialchars($r['nombre'] . ' - ' . $r['descripcion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Contraseña inicial *</label>
                        <input type="password" name="password" class="form-control rounded-3" required minlength="6" placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4" style="background: var(--color-activo-texto);">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Cambiar Contraseña -->
    <div class="modal fade" id="modalCambiarPass" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <h5 class="modal-title fw-bold">Cambiar contraseña: <span id="passNombreUsuario" class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" class="modal-body p-4">
                    <input type="hidden" name="accion" value="cambiar_password">
                    <input type="hidden" name="usuario_id" id="passIdUsuario">
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Nueva contraseña *</label>
                        <input type="password" name="nueva_password" class="form-control rounded-3" required minlength="6">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/sidebar.js"></script>
    <script>
        function abrirModalPass(id, nombre) {
            document.getElementById('passIdUsuario').value = id;
            document.getElementById('passNombreUsuario').textContent = nombre;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCambiarPass')).show();
        }
    </script>
</body>
</html>
