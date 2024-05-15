// Reemplaza esto con tu clave de API
var API_KEY = 'NkrEHNAyfONrhnoxMOYGYfoVzMjBN7Ep';

// Verifica si el navegador soporta la geolocalización
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        // Obtiene las coordenadas del usuario
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        // Define el radio en km
        var radius = 50;

        // Construye la URL de la API
        var url = 'https://app.ticketmaster.com/discovery/v2/events.json?apikey=' + API_KEY + '&keyword=Granada';

        $.ajax({
            type:"GET",
            url: url,
            async:true,
            dataType: "json",
            success: function(json) {
                console.log(json._embedded.events);
                // Muestra los eventos en tarjetas HTML
                var eventsContainer = document.getElementById('events-geo');
                for (let event of json._embedded.events) {
                    var eventCard = document.createElement('div');
                    eventCard.classList.add('event-card');

                    var eventName = document.createElement('h3');
                    eventName.textContent = event.name;
                    eventCard.appendChild(eventName);

                    var eventDate = document.createElement('p');
                    eventDate.textContent = 'Fecha del evento: ' + event.dates.start.localDate;
                    eventCard.appendChild(eventDate);

                    // Verifica si hay imágenes disponibles
                    if (event.images && event.images.length > 0) {
                        var eventImage = document.createElement('img');
                        eventImage.src = event.images[0].url;
                        eventImage.alt = 'Imagen del evento';
                        eventCard.appendChild(eventImage);
                    }

                    var eventUrl = document.createElement('a');
                    eventUrl.href = event.url;
                    eventUrl.textContent = 'Ver más';
                    eventCard.appendChild(eventUrl);

                    eventsContainer.appendChild(eventCard);
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
