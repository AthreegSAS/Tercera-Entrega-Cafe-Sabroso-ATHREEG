// Inicializar el mapa centrado
let map = L.map('map').setView([40.4122901,-3.7122488],6);

// Agregar tileLayer del mapa base desde OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Manejar el cambio de ubicación seleccionada
document.getElementById('select-location').addEventListener('change', function(e) {
    let coords = e.target.value.split(",");
    if (coords.length === 2) {
        map.flyTo([parseFloat(coords[0]), parseFloat(coords[1])], 18);
    }
    
});
