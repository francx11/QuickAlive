$(document).ready(function() {
    $('#nombreActividadBuscado').keyup( function() {
        var nombreActividadBuscado = $(this).val().trim();
        console.log(nombreActividadBuscado);

        // Validar que el campo de búsqueda no esté vacío
        if (nombreActividadBuscado !== '') {
            // Realizar la solicitud AJAX con jQuery
            $.ajax({
                type: 'POST',
                url: 'http://localhost/quickalive/backend/admin/gestionActividades/buscarActividad.php', // Usar una ruta relativa
                data: { nombreActividadBuscado: nombreActividadBuscado },
                dataType: 'json',
                success: function(resultados) {
                    console.log('Sacando resultados');
                    console.log(resultados);
                    mostrarResultados(resultados);
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud AJAX:', status, error);
                }
            });
        } else {
            $('#resultadosBusqueda').empty(); // Limpiar los resultados si el campo está vacío
        }
    });

    function mostrarResultados(resultados) {
        var resultadosDiv = $('#resultadosBusqueda');
        resultadosDiv.empty();
    
        // Iterar sobre los resultados y mostrarlos en el div
        $.each(resultados, function(index, actividad) {
            var actividadDiv = $('<div>').addClass('actividad');
            actividadDiv.append($('<span>').text('Id de actividad: ' + actividad.idActividad));
            actividadDiv.append($('<span>').text('Nombre de actividad: ' + actividad.nombreActividad));
            actividadDiv.append($('<span>').text('Descripción: ' + actividad.descripcion ));
            actividadDiv.append($('<span>').text('Tipo de Actividad: ' + actividad.tipoActividad));
            actividadDiv.append($('<span>').text('Duración: ' + actividad.duracion));
    
            var editarBtn = $('<button>').addClass('editar-btn').text('Editar');
            var eliminarBtn = $('<button>').addClass('eliminar-btn').text('Eliminar');
    
            // Agregar enlaces a los botones de editar y eliminar con los parámetros del usuario
            editarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/gestionActividades/modificarActividad.php?id=' + actividad.idActividad;
            });
    
            eliminarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/gestionActividades/eliminarActividad.php?id=' + actividad.idActividad;
            });
    
            var buttonsDiv = $('<div>').addClass('actividad-buttons');
            buttonsDiv.append(editarBtn);
            buttonsDiv.append(eliminarBtn);
    
            actividadDiv.append(buttonsDiv);
    
            resultadosDiv.append(actividadDiv);
        });
    }

});