/**
 * Sistema de Agenda y Citas Veterinarias - B&G Animal Care
 * - Intervalos de 20 minutos
 * - Lunes: 11:00 a 20:00 | Martes: Cerrado (Rojo) | Mié-Vie: 11:00 a 20:00
 * - Sábado: 11:00 a 17:00 | Domingo: 12:00 a 17:00
 */

const STORAGE_KEY = "bg_animal_care_citas_v1";
const API_URL = "api_citas.php"; // Se sincroniza automáticamente si está en servidor PHP/HostGator

const MESES_ES = [
    "enero", "febrero", "marzo", "abril", "mayo", "junio",
    "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
];

const DIAS_SEMANA_HEADER = ["LUN", "MAR", "MIÉ", "JUE", "VIE", "SÁB", "DOM"];

// Estado global
let fechaActualNavegacion = new Date();
let citasRegistradas = [];
let fechaFiltroAdmin = null;

// =========================================================================
// 1. REGLAS DE HORARIOS CADA 20 MINUTOS POR DÍA DE LA SEMANA
// =========================================================================
function obtenerRangoHorarioPorDiaSemana(dayOfWeek) {
    // JS getDay(): 0 = Domingo, 1 = Lunes, 2 = Martes, 3 = Mié, 4 = Jue, 5 = Vie, 6 = Sáb
    switch (dayOfWeek) {
        case 1: // Lunes: 11:00 am - 8:00 pm
        case 3: // Miércoles: 11:00 am - 8:00 pm
        case 4: // Jueves: 11:00 am - 8:00 pm
        case 5: // Viernes: 11:00 am - 8:00 pm
            return { inicioMin: 11 * 60, finMin: 20 * 60 };
        case 6: // Sábado: 11:00 am - 5:00 pm
            return { inicioMin: 11 * 60, finMin: 17 * 60 };
        case 0: // Domingo: 12:00 pm - 5:00 pm
            return { inicioMin: 12 * 60, finMin: 17 * 60 };
        case 2: // Martes: Cerrado
        default:
            return null;
    }
}

function generarSlots20Min(fechaStr) {
    const [year, month, day] = fechaStr.split("-").map(Number);
    const fechaObj = new Date(year, month - 1, day);
    const rango = obtenerRangoHorarioPorDiaSemana(fechaObj.getDay());

    if (!rango) return []; // Martes cerrado

    const slots = [];
    // Genera cada 20 minutos desde inicio hasta la hora límite inclusive
    // (Si deseas que la última cita termine exactamente a las 8:00 pm / 5:00 pm, cambia <= por <)
    for (let min = rango.inicioMin; min <= rango.finMin; min += 20) {
        const hh = String(Math.floor(min / 60)).padStart(2, "0");
        const mm = String(min % 60).padStart(2, "0");
        slots.push(`${hh}:${mm}`);
    }
    return slots;
}

function formatearHora12h(hora24) {
    const [hStr, mStr] = hora24.split(":");
    let h = parseInt(hStr, 10);
    const ampm = h >= 12 ? "PM" : "AM";
    if (h > 12) h -= 12;
    if (h === 0) h = 12;
    return `${h}:${mStr} ${ampm}`;
}

function formatearFechaLarga(fechaStr) {
    const [y, m, d] = fechaStr.split("-").map(Number);
    return `${d} de ${MESES_ES[m - 1]} de ${y}`;
}

function fechaToISO(dateObj) {
    const y = dateObj.getFullYear();
    const m = String(dateObj.getMonth() + 1).padStart(2, "0");
    const d = String(dateObj.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
}

// =========================================================================
// 2. PERSISTENCIA DE DATOS (API PHP + RESPALDO LOCALSTORAGE)
// =========================================================================
function obtenerCitasEjemploIniciales() {
    const hoy = new Date();
    // Buscar próximos días hábiles (que no sean martes) para precargar ejemplos visuales
    const buscarDiaHabil = (offset) => {
        const f = new Date(hoy);
        f.setDate(hoy.getDate() + offset);
        if (f.getDay() === 2) f.setDate(f.getDate() + 1);
        return fechaToISO(f);
    };

    const d1 = buscarDiaHabil(1);
    const d2 = buscarDiaHabil(2);

    return [
        {
            id: 101,
            fecha: d1,
            hora: "11:20",
            especie: "Perro",
            nombre_mascota: "Rocky",
            motivo: "Vacunación anual y revisión general",
            nombre_propietario: "Carlos Mendoza",
            telefono: "4621234567",
            estado: "Confirmada"
        },
        {
            id: 102,
            fecha: d1,
            hora: "12:00",
            especie: "Gato",
            nombre_mascota: "Luna",
            motivo: "Control dermatológico",
            nombre_propietario: "Fernanda López",
            telefono: "4691098765",
            estado: "Pendiente"
        },
        {
            id: 103,
            fecha: d2,
            hora: "13:40",
            especie: "Perro",
            nombre_mascota: "Max",
            motivo: "Desparasitación y corte de uñas",
            nombre_propietario: "Andrea Ramírez",
            telefono: "4629876543",
            estado: "Pendiente"
        }
    ];
}

async function cargarCitas() {
    // 1. Intentar cargar desde PHP/MySQL si está desplegado en HostGator
    try {
        const resp = await fetch(`${API_URL}?action=listar`, { cache: "no-store" });
        if (resp.ok) {
            const data = await resp.json();
            if (Array.isArray(data.citas)) {
                citasRegistradas = data.citas;
                localStorage.setItem(STORAGE_KEY, JSON.stringify(citasRegistradas));
                return;
            }
        }
    } catch (_) {
        // Si se abre localmente sin PHP, continúa con localStorage
    }

    // 2. Cargar desde localStorage
    const guardadas = localStorage.getItem(STORAGE_KEY);
    if (guardadas) {
        citasRegistradas = JSON.parse(guardadas);
    } else {
        citasRegistradas = obtenerCitasEjemploIniciales();
        localStorage.setItem(STORAGE_KEY, JSON.stringify(citasRegistradas));
    }
}

async function guardarNuevaCita(nuevaCita) {
    // Verificar doble vez que no esté ocupada esa fecha y hora
    const yaOcupada = citasRegistradas.some(
        c => c.fecha === nuevaCita.fecha && c.hora === nuevaCita.hora && c.estado !== "Cancelada"
    );
    if (yaOcupada) {
        alert("Lo sentimos, ese horario acaba de ser reservado. Por favor elige otro.");
        return false;
    }

    nuevaCita.id = Date.now();
    nuevaCita.estado = "Pendiente";

    // Intentar guardar en MySQL vía PHP
    try {
        const resp = await fetch(`${API_URL}?action=crear`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(nuevaCita)
        });
        if (resp.ok) {
            const res = await resp.json();
            if (res.id) nuevaCita.id = res.id;
        }
    } catch (_) {}

    citasRegistradas.push(nuevaCita);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(citasRegistradas));
    return true;
}

async function actualizarEstadoCita(idCita, nuevoEstado) {
    const idx = citasRegistradas.findIndex(c => String(c.id) === String(idCita));
    if (idx === -1) return;

    citasRegistradas[idx].estado = nuevoEstado;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(citasRegistradas));

    try {
        await fetch(`${API_URL}?action=estado`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: idCita, estado: nuevoEstado })
        });
    } catch (_) {}
}

// =========================================================================
// 3. CÁLCULO DE DISPONIBILIDAD Y COLOR GRADUAL POR DÍA
// =========================================================================
function obtenerDisponibilidadDia(fechaStr) {
    const [y, m, d] = fechaStr.split("-").map(Number);
    const fechaObj = new Date(y, m - 1, d);
    const esMartes = fechaObj.getDay() === 2;

    if (esMartes) {
        return {
            esMartes: true,
            totalSlots: 0,
            ocupados: 0,
            disponibles: [],
            claseColor: "day-tuesday-closed",
            porcentaje: 100
        };
    }

    const todosSlots = generarSlots20Min(fechaStr);
    const citasActivasDia = citasRegistradas.filter(
        c => c.fecha === fechaStr && c.estado !== "Cancelada"
    );
    const horasOcupadasSet = new Set(citasActivasDia.map(c => c.hora));

    // Si la fecha es HOY, filtrar horas que ya pasaron
    const ahora = new Date();
    const hoyISO = fechaToISO(ahora);
    const minutosActuales = ahora.getHours() * 60 + ahora.getMinutes();

    const slotsDisponibles = todosSlots.filter(hora => {
        if (horasOcupadasSet.has(hora)) return false;
        if (fechaStr === hoyISO) {
            const [hh, mm] = hora.split(":").map(Number);
            if (hh * 60 + mm <= minutosActuales) return false;
        }
        return true;
    });

    const total = todosSlots.length;
    const ocupadosCount = total - slotsDisponibles.length;
    const ratio = total > 0 ? ocupadosCount / total : 1;

    let claseColor = "occ-0";
    if (slotsDisponibles.length === 0 || ratio >= 1) {
        claseColor = "occ-full"; // Rojo: 100% agotado
    } else if (ratio >= 0.70) {
        claseColor = "occ-high"; // Naranja: >= 70% ocupado
    } else if (ratio >= 0.35) {
        claseColor = "occ-med";  // Amarillo: 35% - 69% ocupado
    } else if (ratio > 0) {
        claseColor = "occ-low";  // Verde suave: 1% - 34% ocupado
    }

    return {
        esMartes: false,
        totalSlots: total,
        ocupados: ocupadosCount,
        disponibles: slotsDisponibles,
        citasActivas: citasActivasDia,
        claseColor,
        porcentaje: Math.round(ratio * 100)
    };
}

// =========================================================================
// 4. GENERADOR DE MENSAJES DE WHATSAPP (PARA EL ADMINISTRADOR)
// =========================================================================
function construirUrlWhatsApp(cita, tipoMensaje = "general") {
    // Limpiar teléfono y asegurar código de país México (52) si tiene 10 dígitos
    let telLimpio = String(cita.telefono).replace(/\D/g, "");
    if (telLimpio.length === 10) {
        telLimpio = "52" + telLimpio;
    }

    const fechaTexto = formatearFechaLarga(cita.fecha);
    const horaTexto = formatearHora12h(cita.hora);

    let mensaje = "";
    if (tipoMensaje === "confirmar") {
        mensaje = `Hola *${cita.nombre_propietario}*, te saludamos de *Veterinaria B&G Animal Care* 🐾. Tu cita para *${cita.nombre_mascota}* (${cita.especie}) el día *${fechaTexto}* a las *${horaTexto}* ha sido *CONFIRMADA*. ¡Te esperamos!`;
    } else if (tipoMensaje === "cancelar") {
        mensaje = `Hola *${cita.nombre_propietario}*, te escribimos de *Veterinaria B&G Animal Care* 🐾 respecto a tu cita de *${cita.nombre_mascota}* programada para el *${fechaTexto}* a las *${horaTexto}*. Te informamos que ha sido cancelada/requiere reagendarse. ¿Qué horario te gustaría elegir?`;
    } else {
        mensaje = `Hola *${cita.nombre_propietario}*, te saludamos de *Veterinaria B&G Animal Care* 🐾. Nos comunicamos para *confirmar* tu cita de *${cita.nombre_mascota}* (${cita.especie}) el día *${fechaTexto}* a las *${horaTexto}* (Motivo: ${cita.motivo}). ¿Nos confirmas tu asistencia o deseas cancelarla?`;
    }

    return `https://wa.me/${telLimpio}?text=${encodeURIComponent(mensaje)}`;
}

// =========================================================================
// 5. RENDERIZADO DEL CALENDARIO (MODO CLIENTE Y MODO ADMIN)
// =========================================================================
function renderizarCalendario() {
    const modo = document.body.dataset.modo; // "cliente" o "admin"
    const grid = document.getElementById("calendarioGrid");
    const labelMes = document.getElementById("nombreMesAnio");
    if (!grid || !labelMes) return;

    const year = fechaActualNavegacion.getFullYear();
    const month = fechaActualNavegacion.getMonth();
    labelMes.textContent = `${MESES_ES[month]} ${year}`;

    grid.innerHTML = "";

    // Encabezados LUN -> DOM
    DIAS_SEMANA_HEADER.forEach(dia => {
        const th = document.createElement("div");
        th.className = "calendar-weekday";
        th.textContent = dia;
        grid.appendChild(th);
    });

    // Primer día del mes ajustado a Lunes = 0 ... Domingo = 6
    const primerDiaMes = new Date(year, month, 1);
    const offsetInicio = (primerDiaMes.getDay() + 6) % 7;
    const diasEnMes = new Date(year, month + 1, 0).getDate();

    const hoy = new Date();
    const hoyISO = fechaToISO(hoy);
    const inicioHoy = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());

    // Celdas vacías iniciales
    for (let i = 0; i < offsetInicio; i++) {
        const emptyCell = document.createElement("div");
        emptyCell.className = "calendar-day empty";
        grid.appendChild(emptyCell);
    }

    // Días del mes
    for (let dia = 1; dia <= diasEnMes; dia++) {
        const fechaObj = new Date(year, month, dia);
        const fechaISO = fechaToISO(fechaObj);
        const esHoy = fechaISO === hoyISO;
        const esPasado = fechaObj < inicioHoy;
        const disp = obtenerDisponibilidadDia(fechaISO);

        const cell = document.createElement("div");
        cell.className = `calendar-day ${disp.claseColor}`;

        if (esPasado && !disp.esMartes) {
            cell.classList.add("day-past");
            if (modo === "cliente") cell.classList.add("client-day-past");
        }

        // Cabecera del número de día
        const headerHtml = `
            <div class="day-header">
                <span class="day-number">${dia}</span>
                ${esHoy ? '<span class="day-today-badge">Hoy</span>' : ""}
            </div>
        `;

        if (modo === "cliente") {
            // ================= VISTA CLIENTE (PRIVACIDAD TOTAL) =================
            let badgeHtml = "";
            let puedeAgendar = false;

            if (disp.esMartes) {
                badgeHtml = `<span class="slot-status-pill pill-cerrado">Cerrado</span>`;
            } else if (esPasado) {
                badgeHtml = `<span class="text-muted" style="font-size:0.7rem;">No disponible</span>`;
            } else if (disp.disponibles.length === 0) {
                badgeHtml = `<span class="slot-status-pill pill-lleno">Agotado</span>`;
            } else {
                puedeAgendar = true;
                const clasePill = disp.porcentaje >= 70 ? "pill-pocos" : "pill-disponible";
                badgeHtml = `<span class="slot-status-pill ${clasePill}">${disp.disponibles.length} horarios</span>`;
            }

            cell.innerHTML = `${headerHtml}<div class="mt-auto">${badgeHtml}</div>`;

            if (puedeAgendar) {
                cell.classList.add("clickable");
                cell.title = `Agendar cita para el ${formatearFechaLarga(fechaISO)}`;
                cell.addEventListener("click", () => abrirModalCliente(fechaISO, disp.disponibles));
            }
        } else {
            // ================= VISTA ADMINISTRADOR (VE TODAS LAS CITAS) =================
            cell.classList.add("clickable");
            let contenidoAdmin = "";

            if (disp.esMartes) {
                contenidoAdmin = `<span class="slot-status-pill pill-cerrado">Martes Cerrado</span>`;
            } else {
                const citasDia = citasRegistradas
                    .filter(c => c.fecha === fechaISO)
                    .sort((a, b) => a.hora.localeCompare(b.hora));

                if (citasDia.length > 0) {
                    const pills = citasDia.map(c => {
                        const claseEstado = c.estado === "Confirmada" ? "confirmada" : (c.estado === "Cancelada" ? "cancelada" : "");
                        return `<div class="admin-cita-pill ${claseEstado}" data-cita-id="${c.id}" title="${formatearHora12h(c.hora)} - ${c.nombre_mascota} (${c.nombre_propietario})">
                            ${formatearHora12h(c.hora)} · ${c.nombre_mascota}
                        </div>`;
                    }).join("");
                    contenidoAdmin = `<div class="admin-citas-stack">${pills}</div>`;
                } else {
                    contenidoAdmin = `<span class="text-muted" style="font-size:0.72rem;">${disp.disponibles.length} libres</span>`;
                }
            }

            cell.innerHTML = `${headerHtml}${contenidoAdmin}`;

            // Clic en píldora específica -> Abre modal detalle de esa cita
            cell.querySelectorAll(".admin-cita-pill").forEach(pill => {
                pill.addEventListener("click", (e) => {
                    e.stopPropagation();
                    abrirModalDetalleCita(pill.dataset.citaId);
                });
            });

            // Clic en la celda del día -> Filtra la lista inferior por ese día
            cell.addEventListener("click", () => {
                fechaFiltroAdmin = fechaISO;
                renderizarListaAdmin();
                document.getElementById("listaCitasAdmin")?.scrollIntoView({ behavior: "smooth", block: "nearest" });
            });
        }

        grid.appendChild(cell);
    }

    // Completar celdas vacías finales para cerrar la última fila de 7
    const totalCeldas = offsetInicio + diasEnMes;
    const resto = totalCeldas % 7;
    if (resto !== 0) {
        for (let i = 0; i < 7 - resto; i++) {
            const emptyEnd = document.createElement("div");
            emptyEnd.className = "calendar-day empty";
            grid.appendChild(emptyEnd);
        }
    }

    if (modo === "admin") {
        actualizarEstadisticasAdmin();
        renderizarListaAdmin();
    }
}

// =========================================================================
// 6. FUNCIONES DE INTERFAZ CLIENTE
// =========================================================================
function abrirModalCliente(fechaISO, horariosDisponibles) {
    document.getElementById("inputFechaCita").value = fechaISO;
    document.getElementById("fechaSeleccionadaTexto").textContent = formatearFechaLarga(fechaISO);

    const selectHora = document.getElementById("horaCita");
    selectHora.innerHTML = `<option value="" selected disabled>Selecciona un horario disponible...</option>`;

    horariosDisponibles.forEach(hora24 => {
        const opt = document.createElement("option");
        opt.value = hora24;
        opt.textContent = formatearHora12h(hora24);
        selectHora.appendChild(opt);
    });

    const infoEl = document.getElementById("infoHorariosDisponibles");
    if (infoEl) {
        infoEl.textContent = `${horariosDisponibles.length} horarios disponibles para este día (intervalos de 20 min).`;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalFormularioCita"));
    modal.show();
}

// =========================================================================
// 7. FUNCIONES DE INTERFAZ ADMINISTRADOR
// =========================================================================
function actualizarEstadisticasAdmin() {
    const year = fechaActualNavegacion.getFullYear();
    const month = String(fechaActualNavegacion.getMonth() + 1).padStart(2, "0");
    const prefijo = `${year}-${month}`;

    const delMes = citasRegistradas.filter(c => c.fecha.startsWith(prefijo));
    const confirmadas = delMes.filter(c => c.estado === "Confirmada").length;
    const pendientes = delMes.filter(c => c.estado === "Pendiente").length;

    const elTotal = document.getElementById("statTotalMes");
    const elConf = document.getElementById("statConfirmadas");
    const elPend = document.getElementById("statPendientes");
    if (elTotal) elTotal.textContent = delMes.length;
    if (elConf) elConf.textContent = confirmadas;
    if (elPend) elPend.textContent = pendientes;
}

function renderizarListaAdmin() {
    const contenedor = document.getElementById("listaCitasAdmin");
    if (!contenedor) return;

    const filtroEstado = document.getElementById("filtroEstado")?.value || "TODOS";
    const filtroEspecie = document.getElementById("filtroEspecie")?.value || "TODAS";
    const busqueda = (document.getElementById("busquedaCita")?.value || "").toLowerCase().trim();

    const etiquetaFecha = document.getElementById("etiquetaFiltroFecha");
    const btnLimpiar = document.getElementById("btnLimpiarFiltroFecha");

    let filtradas = [...citasRegistradas];

    if (fechaFiltroAdmin) {
        filtradas = filtradas.filter(c => c.fecha === fechaFiltroAdmin);
        if (etiquetaFecha) etiquetaFecha.textContent = `Día: ${formatearFechaLarga(fechaFiltroAdmin)}`;
        if (btnLimpiar) btnLimpiar.classList.remove("d-none");
    } else {
        const year = fechaActualNavegacion.getFullYear();
        const month = String(fechaActualNavegacion.getMonth() + 1).padStart(2, "0");
        filtradas = filtradas.filter(c => c.fecha.startsWith(`${year}-${month}`));
        if (etiquetaFecha) etiquetaFecha.textContent = `Mes: ${MESES_ES[fechaActualNavegacion.getMonth()]} ${year}`;
        if (btnLimpiar) btnLimpiar.classList.add("d-none");
    }

    if (filtroEstado !== "TODOS") {
        filtradas = filtradas.filter(c => c.estado === filtroEstado);
    }

    if (filtroEspecie !== "TODAS") {
        if (filtroEspecie === "Otra") {
            filtradas = filtradas.filter(c => !["Perro", "Gato", "Conejo", "Ave"].includes(c.especie));
        } else {
            filtradas = filtradas.filter(c => c.especie === filtroEspecie);
        }
    }

    if (busqueda) {
        filtradas = filtradas.filter(c =>
            c.nombre_mascota.toLowerCase().includes(busqueda) ||
            c.nombre_propietario.toLowerCase().includes(busqueda) ||
            c.telefono.includes(busqueda) ||
            c.motivo.toLowerCase().includes(busqueda)
        );
    }

    filtradas.sort((a, b) => `${a.fecha} ${a.hora}`.localeCompare(`${b.fecha} ${b.hora}`));

    if (filtradas.length === 0) {
        contenedor.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                No hay citas registradas con los filtros seleccionados.
            </div>
        `;
        return;
    }

    contenedor.innerHTML = filtradas.map(cita => {
        const claseCard = cita.estado.toLowerCase();
        const badgeColor = cita.estado === "Confirmada"
            ? "bg-success-subtle text-success"
            : (cita.estado === "Cancelada" ? "bg-danger-subtle text-danger" : "bg-warning-subtle text-warning-emphasis");
        const waUrl = construirUrlWhatsApp(cita, "general");

        return `
            <div class="cita-list-item ${claseCard}">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <strong class="fs-6 text-dark">${cita.nombre_mascota} (${cita.especie})</strong>
                        <span class="badge ${badgeColor} rounded-pill">${cita.estado}</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">${formatearHora12h(cita.hora)}</span>
                    </div>
                    <div class="small text-muted mb-1">
                        <strong>Fecha:</strong> ${formatearFechaLarga(cita.fecha)} · 
                        <strong>Propietario:</strong> ${cita.nombre_propietario} · 
                        <strong>Motivo:</strong> ${cita.motivo}
                    </div>
                    <div>
                        <a href="${waUrl}" target="_blank" rel="noopener" class="wa-phone-link" title="Enviar mensaje por WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i> ${cita.telefono} (Confirmar / Cancelar)
                        </a>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" onclick="abrirModalDetalleCita('${cita.id}')">
                        <i class="bi bi-search me-1"></i> Ver Detalle
                    </button>
                </div>
            </div>
        `;
    }).join("");
}

window.abrirModalDetalleCita = function(idCita) {
    const cita = citasRegistradas.find(c => String(c.id) === String(idCita));
    if (!cita) return;

    document.getElementById("detFecha").textContent = formatearFechaLarga(cita.fecha);
    document.getElementById("detHora").textContent = formatearHora12h(cita.hora);
    document.getElementById("detMascota").textContent = cita.nombre_mascota;
    document.getElementById("detEspecie").textContent = cita.especie;
    document.getElementById("detMotivo").textContent = cita.motivo;
    document.getElementById("detPropietario").textContent = cita.nombre_propietario;
    document.getElementById("detTelefonoTexto").textContent = cita.telefono;

    const badge = document.getElementById("detalleEstadoBadge");
    badge.textContent = cita.estado;
    badge.className = "badge rounded-pill px-3 py-1 mb-1 " + (
        cita.estado === "Confirmada" ? "bg-success text-white" :
        (cita.estado === "Cancelada" ? "bg-danger text-white" : "bg-warning text-dark")
    );

    // Enlace directo al dar clic en el teléfono
    const linkTel = document.getElementById("detTelefonoWa");
    linkTel.href = construirUrlWhatsApp(cita, "general");

    // Botón Confirmar por WhatsApp (actualiza estado a Confirmada y abre WA)
    const btnConfirmar = document.getElementById("btnWaConfirmar");
    btnConfirmar.href = construirUrlWhatsApp(cita, "confirmar");
    btnConfirmar.onclick = async () => {
        await actualizarEstadoCita(cita.id, "Confirmada");
        renderizarCalendario();
        bootstrap.Modal.getInstance(document.getElementById("modalDetalleCita"))?.hide();
    };

    // Botón Cancelar por WhatsApp (actualiza estado a Cancelada, libera horario y abre WA)
    const btnCancelar = document.getElementById("btnWaCancelar");
    btnCancelar.href = construirUrlWhatsApp(cita, "cancelar");
    btnCancelar.onclick = async () => {
        await actualizarEstadoCita(cita.id, "Cancelada");
        renderizarCalendario();
        bootstrap.Modal.getInstance(document.getElementById("modalDetalleCita"))?.hide();
    };

    bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDetalleCita")).show();
};

// =========================================================================
// 8. INICIALIZACIÓN Y EVENTOS
// =========================================================================
document.addEventListener("DOMContentLoaded", async () => {
    await cargarCitas();
    renderizarCalendario();

    // Controles de navegación del mes
    document.getElementById("btnMesAnterior")?.addEventListener("click", () => {
        fechaActualNavegacion.setMonth(fechaActualNavegacion.getMonth() - 1);
        fechaFiltroAdmin = null;
        renderizarCalendario();
    });

    document.getElementById("btnMesSiguiente")?.addEventListener("click", () => {
        fechaActualNavegacion.setMonth(fechaActualNavegacion.getMonth() + 1);
        fechaFiltroAdmin = null;
        renderizarCalendario();
    });

    document.getElementById("btnHoy")?.addEventListener("click", () => {
        fechaActualNavegacion = new Date();
        fechaFiltroAdmin = null;
        renderizarCalendario();
    });

    // Envío del formulario de registro (Cliente y Admin)
    const formCita = document.getElementById("formNuevaCita");
    if (formCita) {
        formCita.addEventListener("submit", async (e) => {
            e.preventDefault();
            const nuevaCita = {
                fecha: document.getElementById("inputFechaCita").value,
                hora: document.getElementById("horaCita").value,
                especie: document.getElementById("especieMascota").value,
                nombre_mascota: document.getElementById("nombreMascota").value.trim(),
                motivo: document.getElementById("motivoConsulta").value.trim(),
                nombre_propietario: document.getElementById("nombrePropietario").value.trim(),
                telefono: document.getElementById("telefonoPropietario").value.replace(/\D/g, "")
            };

            const ok = await guardarNuevaCita(nuevaCita);
            if (!ok) return;

            bootstrap.Modal.getInstance(document.getElementById("modalFormularioCita"))?.hide();
            formCita.reset();
            renderizarCalendario();

            // Si es vista cliente, mostrar recibo de confirmación
            if (document.body.dataset.modo === "cliente") {
                const resumen = document.getElementById("resumenComprobante");
                if (resumen) {
                    resumen.innerHTML = `
                        <div><strong>Mascota:</strong> ${nuevaCita.nombre_mascota} (${nuevaCita.especie})</div>
                        <div><strong>Propietario:</strong> ${nuevaCita.nombre_propietario}</div>
                        <div><strong>Fecha:</strong> ${formatearFechaLarga(nuevaCita.fecha)}</div>
                        <div><strong>Hora:</strong> ${formatearHora12h(nuevaCita.hora)}</div>
                        <div><strong>Motivo:</strong> ${nuevaCita.motivo}</div>
                    `;
                }
                bootstrap.Modal.getOrCreateInstance(document.getElementById("modalComprobante")).show();
            }
        });
    }

    // Eventos exclusivos del panel Administrador
    if (document.body.dataset.modo === "admin") {
        document.getElementById("filtroEstado")?.addEventListener("change", renderizarListaAdmin);
        document.getElementById("filtroEspecie")?.addEventListener("change", renderizarListaAdmin);
        document.getElementById("busquedaCita")?.addEventListener("input", renderizarListaAdmin);

        document.getElementById("btnLimpiarFiltroFecha")?.addEventListener("click", () => {
            fechaFiltroAdmin = null;
            renderizarListaAdmin();
        });

        // Botón "+ Agendar nueva cita" manual para el admin
        const btnManual = document.getElementById("btnAdminNuevaCita");
        const inputFechaManual = document.getElementById("inputFechaCita");
        if (btnManual && inputFechaManual) {
            btnManual.addEventListener("click", () => {
                const hoy = new Date();
                if (hoy.getDay() === 2) hoy.setDate(hoy.getDate() + 1);
                const iso = fechaToISO(hoy);
                inputFechaManual.value = iso;
                actualizarSelectHorasAdmin(iso);
                bootstrap.Modal.getOrCreateInstance(document.getElementById("modalFormularioCita")).show();
            });

            inputFechaManual.addEventListener("change", (e) => {
                actualizarSelectHorasAdmin(e.target.value);
            });
        }
    }
});

function actualizarSelectHorasAdmin(fechaISO) {
    const label = document.getElementById("fechaSeleccionadaTexto");
    const select = document.getElementById("horaCita");
    if (!fechaISO || !select) return;

    if (label) label.textContent = formatearFechaLarga(fechaISO);
    const disp = obtenerDisponibilidadDia(fechaISO);

    if (disp.esMartes) {
        select.innerHTML = `<option value="" disabled selected>Los martes la clínica permanece cerrada</option>`;
        return;
    }

    select.innerHTML = disp.disponibles.length
        ? disp.disponibles.map(h => `<option value="${h}">${formatearHora12h(h)}</option>`).join("")
        : `<option value="" disabled selected>Sin horarios disponibles</option>`;
}