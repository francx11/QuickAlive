$(document).ready(function() {
    // Esta función se ejecuta cuando el documento HTML ha sido completamente cargado

    // Escuchar eventos de teclado en el campo de entrada 'nombreTipoPreferenciaBuscado'
    $('#nombreTipoPreferenciaBuscado').keyup(function() {
        // Obtener el valor del campo de entrada y eliminar espacios en blanco al inicio y al final
        var tipoPreferenciaBuscado = $(this).val().trim();

        // Imprimir el valor del campo de búsqueda en la consola
        console.log(tipoPreferenciaBuscado);

        // Validar que el campo de búsqueda no esté vacío
        if (tipoPreferenciaBuscado !== '') {
            // Realizar una solicitud AJAX utilizando jQuery
            $.ajax({
                type: 'POST', // Método de la solicitud AJAX
                url: 'buscarTipoPreferencia.php', // URL del script PHP para buscar tipos de preferencia
                data: { tipoPreferenciaBuscado: tipoPreferenciaBuscado }, // Datos a enviar al servidor (nombre de tipo de preferencia buscado)
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
        $.each(resultados, function(index, preferencia) {
            // Crear un nuevo div para cada tipo de preferencia encontrado
            var preferenciaDiv = $('<div>').addClass('preferencia');
            // Agregar información de la preferencia al div
            preferenciaDiv.append($('<span>').text('ID de preferencia: ' + preferencia.idTipoPreferencia));
            preferenciaDiv.append($('<span>').text('Tipo de preferencia: ' + preferencia.tipoPreferencia));

            var eliminarBtn = $('<button>').addClass('eliminar-btn').text('Eliminar');

            eliminarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/gestionPreferencias/eliminarTipoPreferencia.php?id=' + preferencia.idTipoPreferencia;
            });

            // Crear un div para los botones y agregar los botones al div
            var buttonsDiv = $('<div>').addClass('preferencia-buttons');
            buttonsDiv.append(eliminarBtn);

            // Agregar el div de botones al div de preferencia
            preferenciaDiv.append(buttonsDiv);

            // Agregar el div de preferencia al contenedor de resultados
            resultadosDiv.append(preferenciaDiv);
        });
    }
});
