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
        //var url = 'https://app.ticketmaster.com/discovery/v2/events.json?apikey=' + API_KEY + '&geoPoint=' + latitude + ',' + longitude + '&radius=' + radius;
        var url = 'https://app.ticketmaster.com/discovery/v2/events.json?apikey=' + API_KEY + '&keyword=Granada';

        // Obtener eventos de la API de TicketMaster
        $.ajax({
            type: "GET",
            url: url,
            async: true,
            dataType: "json",
            success: function(json) {
                var events = json._embedded.events;

                // Obtener idsActividad e idsApi de la tabla ActividadGeolocalizable desde el PHP
                $.ajax({
                    type: 'GET',
                    url: '/quickalive/backend/user/gestionRecomendaciones/obtenerActividadesGeolocalizables.php',
                    dataType: 'json',
                    success: function(response) {
                        var actividades = response; // Array asociativo {idActividad, idApi}

                        

                        // Obtener solo los idApi de las actividades geolocalizables
                        var idsApiGeolocalizable = [];
                        for (var i = 0; i < actividades.length; i++) {
                            idsApiGeolocalizable.push(actividades[i].idApi);
                        }

                        console.log(idsApiGeolocalizable)

                        // Filtrar eventos cuyo idApi no coincida con los idsApiGeolocalizable obtenidos
                        var filteredEvents = [];
                        for (var j = 0; j < events.length; j++) {
                            var event = events[j];
                            // Si no esta en las actividades geolocalizables entonces está disponible
                            if (!idsApiGeolocalizable.includes(event.id)) {
                                filteredEvents.push(event);
                            }
                        }

                        console.log(filteredEvents);

                        // Mostrar eventos filtrados
                        mostrarActividades(filteredEvents);
                    },
                    error: function(xhr, status, err) {
                        console.error('Error al obtener idsActividad e idsApi:', err);
                    }
                });
            },
            error: function(xhr, status, err) {
                console.error('Error al obtener eventos de la API:', err);
            }
        });

        function mostrarActividades(actividades) {
            var eventsContainer = document.getElementById('events-geo');
            eventsContainer.innerHTML = ''; // Limpiar contenedor
        
            actividades.forEach(function(actividad) {
                var eventCard = document.createElement('div');
                eventCard.classList.add('event-card');
        
                var eventName = document.createElement('h3');
                eventName.textContent = actividad.name;
                eventCard.appendChild(eventName);
        
                var eventDescription = document.createElement('p');
                var fecha;
                eventDescription.textContent = 'Fecha del evento: ' +  actividad.dates.start.dateTime;
                fecha =  actividad.dates.start.localDate;

                console.log(actividad.description);

                // Añadir imagen
                var eventImage = document.createElement('img');
                eventImage.src = actividad.images[0].url;
                eventImage.alt = 'Imagen del evento';
                eventCard.appendChild(eventImage);
                
        
                // Añadir botón "Aceptar"
                var btnAceptar = document.createElement('button');
                btnAceptar.textContent = 'Aceptar';
                btnAceptar.addEventListener('click', function() {
                    gestionarActividad(actividad.name, actividad.description, actividad.images[0].url, actividad.id, fecha, 'aceptada');
                    eventCard.remove();
                });
                eventCard.appendChild(btnAceptar);
        
                // Añadir botón "Ver más"
                var btnVerMas = document.createElement('button');
                btnVerMas.textContent = 'Información';
                btnVerMas.addEventListener('click', function() {
                    window.open(actividad.url, '_blank');
                });
                eventCard.appendChild(btnVerMas);
        
                // Añadir botón "Rechazar"
                var btnRechazar = document.createElement('button');
                btnRechazar.textContent = 'Rechazar';
                btnRechazar.addEventListener('click', function() {
                    gestionarActividad(actividad.name, actividad.description, actividad.images[0].url, actividad.id, fecha, 'rechazada');
                    eventCard.remove();
                });
                eventCard.appendChild(btnRechazar);
        
                eventsContainer.appendChild(eventCard);
            });
        }
        
        

        function gestionarActividad(nombreActividad, descripcion, urlRemota, idApi, fechaRealizacion, estado) {
            var urlControlador = '/quickalive/backend/user/gestionRecomendaciones/controladorActividadGeolocalizable.php';
            var parametros = 'estado=' + estado + 
                            '&nombreActividad=' + encodeURIComponent(nombreActividad) +
                            '&descripcion=' + encodeURIComponent(descripcion) +
                            '&urlRemota=' + encodeURIComponent(urlRemota) +
                            '&idApi=' + encodeURIComponent(idApi) +
                            '&fechaRealizacion=' + encodeURIComponent(fechaRealizacion);

            $.ajax({
                type: 'GET',
                url: urlControlador + '?' + parametros,
                success: function(response) {
                    console.log('Actividad gestionada con éxito:', estado);
                },
                error: function(xhr, status, err) {
                    console.error('Error al gestionar la actividad:', err);
                }
            });
        }


    });
}

