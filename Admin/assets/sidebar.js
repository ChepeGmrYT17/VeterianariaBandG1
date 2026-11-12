const barraLateral = document.getElementById('barraLateral');

document.addEventListener('click', (evento)=>{
    if(evento.target.closest('#botonColapsar')){
        const colapsada = document.documentElement.classList.toggle('colapsada');
        localStorage.setItem('sidebar-colapsado', colapsada);
    }

    if(evento.target.closest('#botonMenuMovil')){
        bootstrap.Offcanvas.getOrCreateInstance(barraLateral).toggle();
    }
});