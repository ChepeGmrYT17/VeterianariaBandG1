<?php

$actual = basename($_SERVER['PHP_SELF'], '.php');

$menu = [
    'index' =>['Index', 'fa-house', true],
    'expedientes' =>['Expediente','fa-folder' , false],
    'citas'  =>['Citas', 'fa-chart-simple', false],
    'inventario'      =>['Inventario', 'fa-user-group', false],
    'tienda'  =>['Tienda', 'fa-comment', false],
    'calendario'  =>['Calendario', 'fa-calendar', false],
];
?>


<aside class="barra-lateral offcanvas-lg offcanvas-start" tabindex="-1" id="barraLateral">
    <button type="button" id="botonColapsar" class="boton-colapsar d-none d-lg-flex">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <div class="offcanvas-body">
            <!--Logo-->
        <div class="logo-barra">
            <img src="../assets/img/Logo_b&g.png" alt="Logo" width='40'>
            <span class="etiqueta">B&G Animal Care</span>
            <button type="button" class="boton-cerrar d-lg-none ms-auto"
                data-bs-dismiss="offcanvas" data-bs-target="#barraLateral"
                aria-label="Cerrar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!--Recorremos el menu y generamos un enlace por cada modulo-->
        <nav class="menu-barra">
            <?php foreach($menu as $archivo => [$etiqueta, $icono, $tieneAviso]): ?>
                <a href="<?= $archivo?>.php" class="item-nav <?=$actual === $archivo ? 'activo' : '' ?>"
                title="<?=$etiqueta ?>">
                    <i class="fa-solid <?=$icono ?>"></i>
                    <span class="etiqueta"><?=$etiqueta?></span>
                    <?php if($tieneAviso): ?>
                        <span class="punto-aviso"></span>
                    <?php endif;?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="parte-baja">
            <a href="settings.php" class="item-nav <?=$actual === 'settings' ? 'activo' : ''?>" title="Settings">
                <i class="fa-solid fa-gear"></i>
                <span class="etiqueta">Settings</span>
            </a>
            <a href="profile.php" class="item-nav" title="My Profile">
                <span class="avatar">
                    <i class="fa-solid fa-user"></i>
                    <span class="indicador-conectado"></span>
                </span>
                <span class="etiqueta">My Profile</span>
            </a>
        </div>
    </div>

</aside>
