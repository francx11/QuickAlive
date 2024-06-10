$(document).ready(function() {
    // Realizar la solicitud AJAX para obtener las actividades recomendadas
    $.ajax({
        url: 'http://localhost/quickalive/backend/user/gestionRecomendaciones/obtenerActividadesSimples.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Verificar si se obtuvieron actividades
            if (response && response.length > 0) {
                console.log(response);

            // Limpiar el contenido actual del contenedor de eventos
            $('#events-simple').empty();


            // Recorrer las actividades y mostrarlas en el contenedor de eventos
            response.forEach(function(actividad) {
                var actividadHTML = '<div class="actividad">';
                    
                    var eventName = actividad.nombreActividad;
                    var eventDescription = actividad.descripcion;
                    var eventId = actividad.idActividad;

                    actividadHTML += '<h4>' + eventName + '</h4>';
                    //actividadHTML += '<p>Duración: ' + eventDuration + ' minutos</p>';

                    // Añadir imagen si está disponible
                    if (actividad.fotos && actividad.fotos.length > 0) {
                        var eventImage = actividad.fotos[0];
                        actividadHTML += '<img src="' + eventImage + '" alt="Imagen de la actividad">';
                    }

                    // Añadir botones
                    actividadHTML += '<div class="button-container">';
                    actividadHTML += '<button class="btn-aceptar" data-id="' + eventId + '">Aceptar</button>';
                    actividadHTML += '<button class="btn-info" data-descripcion="' + eventDescription + '">Información</button>';
                    actividadHTML += '<button class="btn-rechazar" data-id="' + eventId + '">Rechazar</button>';
                    actividadHTML += '</div>'; // Cerrar button-container

                    actividadHTML += '</div>'; // Cerrar actividad

                    $('#events-simple').append(actividadHTML);

            });

            // Escuchar eventos de clic en los botones de aceptación, rechazo e información
            $('#events-simple').on('click', '.btn-aceptar', function() {
                var idActividad = $(this).data('id');
                // Ejecutar PHP con estado "aceptada"
                $.get('/quickalive/backend/user/gestionRecomendaciones/controladorInteresActividad.php', { estado: 'aceptada', idActividad: idActividad }, function(response) {
                    // Manejar la respuesta del servidor
                    console.log('Respuesta del servidor:', response);
                    // Si necesitas realizar alguna acción adicional basada en la respuesta, puedes hacerlo aquí
                });

                $(this).closest('.actividad').remove();
            });

            $('#events-simple').on('click', '.btn-rechazar', function() {
                var idActividad = $(this).data('id');
                // Ejecutar PHP con estado "rechazada"
                $.get('/quickalive/backend/user/gestionRecomendaciones/controladorInteresActividad.php', { estado: 'rechazada', idActividad: idActividad }, function(response) {
                    // Manejar la respuesta del servidor
                    console.log('Respuesta del servidor:', response);
                    // Si necesitas realizar alguna acción adicional basada en la respuesta, puedes hacerlo aquí
                });
                $(this).closest('.actividad').remove();
            });            

          

            // Función para cerrar el iframe
            function cerrarIframe() {
                var iframe = document.querySelector("iframe");
                iframe.parentNode.removeChild(iframe);
            }

            $('#events-simple').on('click', '.btn-info', function() {
                // Crear un elemento iframe
                var iframe = document.createElement("iframe");
                
                // Establecer atributos del iframe
                iframe.frameBorder = 0;
                iframe.width = "100%";
                iframe.height = "300px";
                iframe.style.position = "absolute";
                iframe.style.top = "0";
                iframe.style.left = "0";
                
                // Obtener la descripción de la actividad
                var descripcion = $(this).data('descripcion');
                
                // Establecer el contenido del iframe con la descripción y el botón de cierre
                iframe.srcdoc = '<div style="padding: 20px; background-color: white;">' + descripcion + '</div><button onclick="cerrarIframe()">Cerrar</button>';
                
                // Agregar el iframe al contenedor de eventos
                document.getElementById("events-simple").appendChild(iframe);
            });

            } else {
                // No se encontraron actividades recomendadas
                $('#events-simple').html('<p>No se encontraron actividades recomendadas.</p>');
            }
        },
        error: function(xhr, status, error) {
            // Manejar errores de la solicitud AJAX
            console.error('Error en la solicitud AJAX:', error);
            $('#events-simple').html('<p>Error al cargar las actividades recomendadas.</p>');
        }
    });
});



