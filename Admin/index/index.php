<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
      <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (iconos) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Nuestro CSS va DESPUÉS de Bootstrap para poder sobrescribirlo -->
    <link href="../assets/sidebar.css" rel="stylesheet">
    <script>
        // Anti-parpadeo: recordamos si el sidebar estaba colapsado.
        // (En celular esta clase no tiene efecto, el CSS la ignora.)
        if (localStorage.getItem('sidebar-colapsado') === 'true') {
        document.documentElement.classList.add('colapsada');
        }
    </script>
</head>
<body>
    <div class="layout">
        <?php include '../includes/sidebar.php';?>
        <main id="contenido">
        <!-- Botón hamburguesa: sirve para colapsar (escritorio) y abrir menú (celular) -->
        <button id="botonMenuMovil" class="boton-menu-movil mb-3 d-lg-none" aria-label="Menú">
            <i class="fa-solid fa-bars"></i>
        </button>



        ... contenido del módulo ...


        
        </main>
    </div>
    
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/sidebar.js"></script>
</html>