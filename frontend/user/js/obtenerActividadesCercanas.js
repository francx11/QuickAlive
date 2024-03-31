// Reemplaza esto con tu clave de API
var API_KEY = 'NkrEHNAyfONrhnoxMOYGYfoVzMjBN7Ep';

// Verifica si el navegador soporta la geolocalización
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        // Obtiene las coordenadas del usuario
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        // Define el radio en km
        var radius = 1000;

        // Construye la URL de la API
        var url = 'https://app.ticketmaster.com/discovery/v2/events.json?apikey=' + API_KEY + '&geoPoint=' + latitude + ',' + longitude + '&radius=' + radius;

        $.ajax({
            type:"GET",
            url: url,
            async:true,
            dataType: "json",
            success: function(json) {
                console.log(json);
                // Imprime los eventos
                for (let event of json._embedded.events) {
                    console.log('Nombre del evento: ' + event.name);
                    console.log('Fecha del evento: ' + event.dates.start.localDate);
                    console.log('URL del evento: ' + event.url);
                    console.log('---');
                }
            },
            error: function(xhr, status, err) {
                console.error('Error:', err);
            }
        });
    });
} else {
    console.log('La geolocalización no está disponible en este navegador.');
}
