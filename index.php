<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>B&G Animal Care | Clínica Veterinaria</title>
    
    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 & Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <!-- Icono logo en navegador -->
    <link id="favicon" rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="landing_page.css">
</head>
<body>

    <!-- ================= NAVBAR ================= -->
    <div class="sticky-top">
        <header class="encabezado">
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a href="#inicio" class="navbar-brand d-flex align-items-center gap-2">
                        <img src="img/Logo_b&g.png" alt="Logo B&G" height="55" width="55" class="object-fit-contain">
                        <span class="logo-texto">
                            <strong>B&amp;G Animal Care</strong>
                            <small>Clínica Veterinaria</small>
                        </span>
                    </a>

                    <!-- Botón Hamburguesa -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir Menú">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Menú Desplegable -->
                    <div class="collapse navbar-collapse" id="menuPrincipal">
                        <ul class="navbar-nav mx-auto mb-3 mb-lg-0 gap-lg-4 align-items-center">
                            <li class="nav-item"><a href="#inicio" class="nav-link active">Inicio</a></li>
                            <li class="nav-item"><a href="#servicios" class="nav-link">Servicios</a></li>
                            <li class="nav-item"><a href="#nosotros" class="nav-link">Nosotros</a></li>
                            <li class="nav-item"><a href="#contacto" class="nav-link">Contacto</a></li>
                        </ul>
                        <div class="d-flex align-items-center justify-content-center gap-2 mt-3 mt-lg-0">
                            <!-- Botón Acceso al Sistema -->
                            <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalLogin">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Ingresar
                            </button>
                            <!-- Botón Agendar Cita -> Redirige a agendar_cita.html -->
                            <a href="agendar_cita.html" class="btn boton-vet boton-vet-peq">
                                <i class="bi bi-calendar3"></i> Agendar cita
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
    </div>
    
    <main>
        <!-- ================= HERO ================= -->
        <section class="portada" id="inicio">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6 portada-contenido">
                        <h1 class="portada-titulo">
                            Cuidado experto,
                            <span class="acento">amor verdadero</span>
                            <i class="fa-regular fa-heart corazon-decorativo"></i>
                        </h1>
                        <p class="portada-texto">
                            En B&amp;G Animal Care brindamos atención veterinaria integral para mejorar la calidad de vida de tu mascota.
                        </p>

                        <ul class="caracteristicas list-unstyled">
                            <li>
                                <span class="caracteristica-icono"><i class="fa-solid fa-hand-holding-heart"></i></span>
                                <span class="caracteristica-texto">Atención <br>personalizada</span>
                            </li>
                            <li>
                                <span class="caracteristica-icono"><i class="fa-solid fa-shield-halved"></i></span>
                                <span class="caracteristica-texto">Profesionales <br>comprometidos</span>
                            </li>
                            <li>
                                <span class="caracteristica-icono"><i class="fa-solid fa-stethoscope"></i></span>
                                <span class="caracteristica-texto">Tecnología <br>avanzada</span>
                            </li>
                            <li>
                                <span class="caracteristica-icono"><i class="fa-solid fa-paw"></i></span>
                                <span class="caracteristica-texto">Bienestar <br>animal</span>
                            </li>
                        </ul>
                        <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                            <a href="agendar_cita.html" class="btn boton-vet boton-vet-grand">
                                <i class="bi bi-calendar-plus"></i> Agendar cita ahora
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="portada-imagen-caja">
                            <img src="img/portada-mascotas.png" alt="Mascotas atendidas en B&G" class="portada-imagen">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SERVICIOS ================= -->
        <section class="seccion-padding" id="servicios">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="badge-subtitulo">Lo que ofrecemos</span>
                    <h2 class="seccion-titulo">Servicios Médicos Veterinarios</h2>
                    <p class="seccion-descripcion">Atención clínica de primer nivel adaptada a las necesidades de tu perro o gato.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="tarjeta-servicio">
                            <div class="icono-servicio"><i class="fa-solid fa-notes-medical"></i></div>
                            <h3>Consulta General</h3>
                            <p>Evaluación médica, diagnóstico preventivo, seguimiento de tratamientos y expedientes clínicos digitales.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="tarjeta-servicio">
                            <div class="icono-servicio"><i class="fa-solid fa-syringe"></i></div>
                            <h3>Vacunación y Desparasitación</h3>
                            <p>Esquemas completos de inmunización para cachorros y adultos, junto a control de parásitos internos y externos.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="tarjeta-servicio">
                            <div class="icono-servicio"><i class="fa-solid fa-kit-medical"></i></div>
                            <h3>Cirugía y Quirófano</h3>
                            <p>Esterilizaciones y cirugías de tejidos blandos bajo constante supervisión anestésica y monitoreo de signos vitales.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="tarjeta-servicio">
                            <div class="icono-servicio"><i class="fa-solid fa-vial-virus"></i></div>
                            <h3>Estudios de Laboratorio</h3>
                            <p>Análisis clínicos y biometrías hemáticas para detectar oportunamente cualquier afección o patología.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="tarjeta-servicio">
                            <div class="icono-servicio"><i class="fa-solid fa-pills"></i></div>
                            <h3>Farmacia Especializada</h3>
                            <p>Medicamentos avalados, antibióticos, analgésicos, suplementos y dietas médicas de prescripción.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="tarjeta-servicio">
                            <div class="icono-servicio"><i class="fa-solid fa-shower"></i></div>
                            <h3>Higiene y Estética</h3>
                            <p>Baños terapéuticos, medicados y cuidados estéticos para asegurar la higiene dermatológica de tu compañero.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= NOSOTROS ================= -->
        <section class="seccion-nosotros" id="nosotros">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <div class="tarjeta-nosotros-box p-4 p-md-5">
                            <span class="badge-subtitulo">Sobre B&amp;G</span>
                            <h2 class="seccion-titulo text-start mb-3">Vocación por el Cuidado y la Salud Animal</h2>
                            <p class="text-muted">
                                En <strong>B&amp;G Animal Care</strong> combinamos tecnología diagnóstica con trato compasivo. Sabemos que cada paciente es parte de una familia, por lo que cada tratamiento se diseña con transparencia, respeto y seguimiento constante.
                            </p>
                            <ul class="list-unstyled d-grid gap-3 mt-4 mb-4">
                                <li class="d-flex align-items-center gap-3">
                                    <i class="fa-solid fa-circle-check text-primary fs-5"></i>
                                    <span>Seguimiento y control de historial médico.</span>
                                </li>
                                <li class="d-flex align-items-center gap-3">
                                    <i class="fa-solid fa-circle-check text-primary fs-5"></i>
                                    <span>Material e instalaciones quirúrgicas con altos estándares.</span>
                                </li>
                                <li class="d-flex align-items-center gap-3">
                                    <i class="fa-solid fa-circle-check text-primary fs-5"></i>
                                    <span>Recordatorios directos vía WhatsApp para sus próximas consultas.</span>
                                </li>
                            </ul>
                            <a href="agendar_cita.html" class="btn boton-vet boton-vet-peq">Agendar consulta</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="tarjeta-urgencia p-4 p-md-5 text-white">
                            <i class="fa-solid fa-truck-medical fs-1 mb-3"></i>
                            <h3 class="fw-bold mb-3">¿Tu mascota presenta una urgencia?</h3>
                            <p class="mb-4 text-white-50">Llámanos inmediatamente para orientarte y tener listo el equipo clínico a tu arribo.</p>
                            <a href="tel:+524691267790" class="btn btn-light rounded-pill px-4 py-3 fw-bold text-dark">
                                <i class="bi bi-telephone-fill me-2 text-danger"></i> Llamar al médico de guardia
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= CONTACTO Y UBICACIÓN ================= -->
        <section class="seccion-padding" id="contacto">
            <div class="container">
                <div class="text-center mb-5">
                    <span class="badge-subtitulo">Ubicación y Horarios</span>
                    <h2 class="seccion-titulo">Visítanos en Nuestra Clínica</h2>
                    <p class="seccion-descripcion">Estamos ubicados convenientemente para atenderte con cita previa.</p>
                </div>

                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-5">
                        <div class="d-flex flex-column gap-3 h-100 justify-content-between">
                            <div class="tarjeta-contacto">
                                <div class="contacto-icono"><i class="fa-solid fa-location-dot"></i></div>
                                <div>
                                    <h5 class="fw-bold mb-1">Dirección</h5>
                                    <p class="text-muted mb-0">Veterinaria B&amp;G Animal Care, Pénjamo, Guanajuato.</p>
                                </div>
                            </div>
                            <div class="tarjeta-contacto">
                                <div class="contacto-icono"><i class="fa-regular fa-clock"></i></div>
                                <div>
                                    <h5 class="fw-bold mb-1">Horario de Atención</h5>
                                    <p class="text-muted mb-0">
                                        Lun, Mié, Jue y Vie: 11:00 AM – 8:00 PM<br>
                                        Sábados: 11:00 AM – 5:00 PM | Domingos: 12:00 PM – 5:00 PM<br>
                                        <span class="text-danger fw-semibold">Martes: Cerrado</span>
                                    </p>
                                </div>
                            </div>
                            <div class="tarjeta-contacto">
                                <div class="contacto-icono"><i class="fa-brands fa-whatsapp"></i></div>
                                <div>
                                    <h5 class="fw-bold mb-1">Atención por WhatsApp</h5>
                                    <p class="text-muted mb-0">+52 (469) 132-3718 · Dudas y confirmación de citas</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mapa de Google Maps -->
                    <div class="col-lg-7">
                        <div class="mapa-caja h-100">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3738.8060103333833!2d-101.71964489999999!3d20.4320627!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842c13c632c0bea5%3A0xb654a2e99ffec23e!2sVeterinaria%20B%26G%20Animal%20Care!5e0!3m2!1ses-419!2smx!4v1794492188647!5m2!1ses-419!2smx"
                                width="100%" 
                                height="100%" 
                                style="border:0; min-height: 350px;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="pie-pagina text-white py-4">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-paw fs-4 text-info"></i>
                <span class="fw-bold">B&amp;G Animal Care</span> · Bienestar para quienes más quieres.
            </div>
            <div class="text-center text-md-end text-white-50 small">
                © <span id="anioActual"></span> B&amp;G Animal Care. Desarrollado por <strong>Dog's Computer TI</strong>.
            </div>
        </div>
    </footer>

    <!-- ================= BOTÓN FLOTANTE WHATSAPP ================= -->
    <a href="https://wa.me/5214691323718?text=Hola,%20solicito%20informaci%C3%B3n%20sobre%20sus%20servicios%20veterinarios" 
       target="_blank" 
       rel="noopener" 
       class="btn-flotante-wsp" 
       aria-label="Contactar por WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- ================= MODAL LOGIN SISTEMA ================= -->
    <div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 p-3">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="modalLoginLabel">
                        <i class="fa-solid fa-shield-cat me-2"></i>Acceso al Personal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body pt-3">
                    <p class="text-muted small mb-4">Ingresa con tus credenciales asignadas para administrar citas, expedientes e inventario.</p>
                    <form action="admin_citas.html" method="GET">
                        <div class="mb-3">
                            <label for="correo" class="form-label fw-semibold small">Correo Electrónico</label>
                            <input type="email" class="form-control rounded-3 py-2" id="correo" required placeholder="personal@bgvet.mx">
                        </div>
                        <div class="mb-4">
                            <label for="clave" class="form-label fw-semibold small">Contraseña</label>
                            <input type="password" class="form-control rounded-3 py-2" id="clave" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn boton-vet w-100 justify-content-center py-2">
                            Iniciar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap y lógica personalizada -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="landing_page.js"></script>
</body>
</html>