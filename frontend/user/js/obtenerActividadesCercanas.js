// Verifica si el navegador soporta la geolocalización
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        // Obtiene las coordenadas del usuario
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        // Define el radio en km
        var radius = 50;

        // Construye la URL para tu servidor PHP
        var url = 'http://localhost/quickalive/backend/api/variablesEntorno/variablesTicketMaster.php?latitude=' + latitude + '&longitude=' + longitude + '&radius=' + radius;
        //var url = 'http://localhost/quickalive/backend/api/variablesEntorno/variablesTicketMaster.php'
        // Obtener eventos desde tu servidor PHP
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

                        console.log(idsApiGeolocalizable);

                        // Filtrar eventos cuyo idApi no coincida con los idsApiGeolocalizable obtenidos
                        var filteredEvents = [];
                        for (var j = 0; j < events.length; j++) {
                            var event = events[j];
                            // Si no está en las actividades geolocalizables entonces está disponible
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
                // Crear la tarjeta del evento
                var eventCard = document.createElement('div');
                eventCard.classList.add('actividad');

                var eventName = document.createElement('h4');
                eventName.textContent = actividad.name;
                eventCard.appendChild(eventName);
        
                // Añadir la imagen del evento
                var eventImage = document.createElement('img');
                eventImage.src = actividad.images[0].url;
                eventImage.alt = 'Imagen del evento';
                eventCard.appendChild(eventImage);
        
                // Crear el contenedor de botones
                var buttonContainer = document.createElement('div');
                buttonContainer.classList.add('button-container');

                var localDate = actividad.dates.start.localDate;
                var localTime = actividad.dates.start.localTime;

                var combinedDateTime = `${localDate} ${localTime}`;

                console.log(combinedDateTime);
        
                // Añadir botón "Aceptar"
                var btnAceptar = document.createElement('button');
                btnAceptar.textContent = 'Aceptar';
                btnAceptar.addEventListener('click', function() {
                    gestionarActividad(actividad.name, actividad.description, actividad.images[0].url, actividad.id, combinedDateTime, 'aceptada');
                    eventCard.remove();
                });
                buttonContainer.appendChild(btnAceptar);
        
                // Añadir botón "Ver más"
                var btnVerMas = document.createElement('button');
                btnVerMas.textContent = 'Información';
                btnVerMas.addEventListener('click', function() {
                    window.open(actividad.url, '_blank');
                });
                buttonContainer.appendChild(btnVerMas);
        
                // Añadir botón "Rechazar"
                var btnRechazar = document.createElement('button');
                btnRechazar.textContent = 'Rechazar';
                btnRechazar.addEventListener('click', function() {
                    gestionarActividad(actividad.name, actividad.description, actividad.images[0].url, actividad.id, combinedDateTime, 'rechazada');
                    eventCard.remove();
                });
                buttonContainer.appendChild(btnRechazar);
        
                // Añadir el contenedor de botones a la tarjeta del evento
                eventCard.appendChild(buttonContainer);
        
                // Añadir la tarjeta del evento al contenedor principal
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
