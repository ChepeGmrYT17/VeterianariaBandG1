<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Si ya inició sesión, mandarlo directo al panel
if (!empty($_SESSION['usuario_id'])) {
    header("Location: index/index.php");
    exit;
}

$error = '';
$mensajeInfo = '';

if (isset($_GET['cerrada'])) {
    $mensajeInfo = 'Has cerrado sesión correctamente.';
} elseif (isset($_GET['error']) && $_GET['error'] === 'inactivo') {
    $error = 'Tu cuenta se encuentra desactivada. Contacta al administrador.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Por favor ingresa tu correo y contraseña.';
    } else {
        $stmt = $pdo->prepare("
            SELECT u.id, u.nombre, u.email, u.password, u.activo, r.nombre AS rol
            FROM usuarios u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE u.email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            if ((int)$usuario['activo'] !== 1) {
                $error = 'Esta cuenta está desactivada. Contacta al administrador.';
            } else {
                session_regenerate_id(true);
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email']  = $usuario['email'];
                $_SESSION['usuario_rol']    = $usuario['rol'];

                header("Location: index/index.php");
                exit;
            }
        } else {
            $error = 'Correo electrónico o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | B&amp;G Animal Care</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --color-activo-fondo: #d4e6fa;
            --color-activo-texto: #10244d;
            --color-primario: #0d5cb6;
        }
        body {
            background: #f8f9fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            border-radius: 20px;
            border: 1px solid #eef2f6;
            box-shadow: 0 12px 32px rgba(15, 23, 42, .08);
            padding: 2.3rem 2rem;
        }
        .login-logo-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 1.6rem;
        }
        .login-logo-bg {
            width: 74px;
            height: 74px;
            border-radius: 18px;
            background: var(--color-activo-fondo);
            display: grid;
            place-items: center;
            margin-bottom: 0.9rem;
        }
        .login-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--color-activo-texto);
            margin: 0;
        }
        .form-control {
            border-radius: 12px;
            padding: 0.72rem 0.95rem;
            border-color: #e2e8f0;
        }
        .form-control:focus {
            border-color: var(--color-primario);
            box-shadow: 0 0 0 3px rgba(13, 92, 182, 0.15);
        }
        .btn-login {
            background: var(--color-activo-texto);
            color: #fff;
            border: 0;
            border-radius: 12px;
            padding: 0.78rem;
            font-weight: 600;
            width: 100%;
            transition: background .2s, transform .15s;
        }
        .btn-login:hover {
            background: var(--color-primario);
            color: #fff;
            transform: translateY(-1px);
        }
        .demo-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 0.75rem 0.9rem;
            font-size: 0.78rem;
            color: #475569;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-logo-box">
            <div class="login-logo-bg">
                <img src="assets/img/Logo_b&g.png" alt="Logo B&G" width="48">
            </div>
            <h1 class="login-title">B&amp;G Animal Care</h1>
            <span class="text-muted small">Panel Administrativo · Acceso al Personal</span>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger py-2 px-3 rounded-3 small d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($mensajeInfo !== ''): ?>
            <div class="alert alert-success py-2 px-3 rounded-3 small d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span><?= htmlspecialchars($mensajeInfo) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" autocomplete="off">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold small text-secondary">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" class="form-control border-start-0" id="email" name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           placeholder="usuario@bgvet.mx" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold small text-secondary">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" class="form-control border-start-0 border-end-0" id="password" name="password"
                           placeholder="••••••••" required>
                    <button type="button" class="input-group-text bg-light border-start-0 rounded-end-3 text-muted" id="togglePass" title="Mostrar contraseña">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-login mb-3">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Iniciar sesión
            </button>
        </form>

        <div class="text-center mb-3">
            <a href="../index.php" class="text-decoration-none small fw-semibold text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al sitio web
            </a>
        </div>

        <!-- Accesos iniciales de prueba (puedes eliminar este bloque en producción) -->
        <div class="demo-box mt-2">
            <strong class="d-block text-dark mb-1"><i class="fa-solid fa-key me-1 text-primary"></i> Cuentas iniciales de prueba:</strong>
            <div><strong>Admin:</strong> admin@bgvet.mx / <code>Admin123*</code></div>
            <div><strong>Veterinario:</strong> veterinario@bgvet.mx / <code>Vet123*</code></div>
            <div><strong>Recepción:</strong> recepcion@bgvet.mx / <code>Recep123*</code></div>
        </div>
    </div>

    <script>
        const btnToggle = document.getElementById('togglePass');
        const inputPass = document.getElementById('password');
        btnToggle.addEventListener('click', () => {
            const esPass = inputPass.type === 'password';
            inputPass.type = esPass ? 'text' : 'password';
            btnToggle.innerHTML = esPass ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
        });
    </script>
</body>
</html>
