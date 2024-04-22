$(document).ready(function() {
    // Realizar la solicitud AJAX para obtener las actividades recomendadas
    $.ajax({
        url: 'http://localhost/quickalive/backend/user/gestionRecomendaciones/obtenerActividadesSimples.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log(response)
            // Verificar si se obtuvieron actividades
            if (response && response.length > 0) {
                // Limpiar el contenido actual del contenedor de eventos
                $('#events-container').empty();

                // Recorrer las actividades y mostrarlas en el contenedor de eventos
                response.forEach(function(actividad) {
                    var actividadHTML = '<div class="actividad">';
                    actividadHTML += '<h3>' + actividad.nombreActividad + '</h3>';
                    actividadHTML += '<p>' + actividad.descripcion + '</p>';
                    actividadHTML += '<p>Duración: ' + actividad.duracion + ' minutos</p>';
                    actividadHTML += '</div>';

                    $('#events-container').append(actividadHTML);
                });
            } else {
                // No se encontraron actividades recomendadas
                $('#events-container').html('<p>No se encontraron actividades recomendadas.</p>');
            }
        },
        error: function(xhr, status, error) {
            // Manejar errores de la solicitud AJAX
            console.error('Error en la solicitud AJAX:', error);
            $('#events-container').html('<p>Error al cargar las actividades recomendadas.</p>');
        }
    });
});
