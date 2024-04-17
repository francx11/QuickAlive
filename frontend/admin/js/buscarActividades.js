$(document).ready(function() {
    // Esta función se ejecuta cuando el documento HTML ha sido completamente cargado

    // Escuchar eventos de teclado en el campo de entrada 'nombreActividadBuscado'
    $('#nombreActividadBuscado').keyup(function() {
        // Obtener el valor del campo de entrada y eliminar espacios en blanco al inicio y al final
        var nombreActividadBuscado = $(this).val().trim();

        // Imprimir el valor del campo de búsqueda en la consola
        console.log(nombreActividadBuscado);

        // Validar que el campo de búsqueda no esté vacío
        if (nombreActividadBuscado !== '') {
            // Realizar una solicitud AJAX utilizando jQuery
            $.ajax({
                type: 'POST', // Método de la solicitud AJAX
                url: 'buscarActividad.php', // URL del script PHP para buscar actividades
                data: { nombreActividadBuscado: nombreActividadBuscado }, // Datos a enviar al servidor (nombre de actividad buscado)
                dataType: 'json', // Tipo de datos esperados en la respuesta (JSON)
                success: function(resultados) {
                    // Función que se ejecuta si la solicitud AJAX se realiza con éxito
                    console.log('Sacando resultados');
                    console.log(resultados);
                    // Llamar a la función para mostrar los resultados obtenidos
                    mostrarResultados(resultados);
                },
                error: function(xhr, status, error) {
                    // Función que se ejecuta si la solicitud AJAX falla
                    console.error('Error en la solicitud AJAX:', status, error);
                }
            });
        } else {
            // Limpiar el contenedor de resultados si el campo de búsqueda está vacío
            $('#resultadosBusqueda').empty();
        }
    });

    // Función para mostrar los resultados de la búsqueda en el documento HTML
    function mostrarResultados(resultados) {
        // Seleccionar el contenedor de resultados
        var resultadosDiv = $('#resultadosBusqueda');
        // Vaciar el contenedor antes de agregar nuevos resultados
        resultadosDiv.empty();

        // Iterar sobre los resultados y mostrarlos en el div de resultados
        $.each(resultados, function(index, actividad) {
            // Crear un nuevo div para cada actividad encontrada
            var actividadDiv = $('<div>').addClass('actividad');
            // Agregar información de la actividad al div
            actividadDiv.append($('<span>').text('ID de actividad: ' + actividad.idActividad));
            actividadDiv.append($('<span>').text('Nombre de actividad: ' + actividad.nombreActividad));
            actividadDiv.append($('<span>').text('Descripción: ' + actividad.descripcion ));
            actividadDiv.append($('<span>').text('Duración: ' + actividad.duracion));

            // Crear botones de editar y eliminar
            var editarBtn = $('<button>').addClass('editar-btn').text('Editar');
            var eliminarBtn = $('<button>').addClass('eliminar-btn').text('Eliminar');

            // Agregar enlaces a los botones de editar y eliminar con los parámetros de la actividad
            editarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/gestionActividades/modificarActividad.php?id=' + actividad.idActividad;
            });

            eliminarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/gestionActividades/eliminarActividad.php?id=' + actividad.idActividad;
            });

            // Crear un div para los botones y agregar los botones al div
            var buttonsDiv = $('<div>').addClass('actividad-buttons');
            buttonsDiv.append(editarBtn);
            buttonsDiv.append(eliminarBtn);

            // Agregar el div de botones al div de actividad
            actividadDiv.append(buttonsDiv);

            // Agregar el div de actividad al contenedor de resultados
            resultadosDiv.append(actividadDiv);
        });
    }

});
