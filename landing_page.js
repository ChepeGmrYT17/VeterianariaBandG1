document.addEventListener("DOMContentLoaded", () => {
    // 1. Cierre automático del menú móvil de Bootstrap al hacer clic en un enlace
    const menu = document.getElementById("menuPrincipal");
    const links = document.querySelectorAll(".navbar .nav-link");

    links.forEach(link => {
        link.addEventListener("click", () => {
            if (menu && menu.classList.contains("show")) {
                bootstrap.Collapse.getOrCreateInstance(menu).hide();
            }
        });
    });

    // 2. Resaltar enlace activo según la sección en pantalla (ScrollSpy nativo)
    const sections = document.querySelectorAll("main section[id]");
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                links.forEach(l => {
                    l.classList.toggle("active", l.getAttribute("href") === `#${entry.target.id}`);
                });
            }
        });
    }, { rootMargin: "-40% 0px -50% 0px" });

    sections.forEach(s => observer.observe(s));

    // 3. Año dinámico en el footer
    const spanAnio = document.getElementById("anioActual");
    if (spanAnio) {
        spanAnio.textContent = new Date().getFullYear();
    }
});