$(document).ready(function() {
    $('#nombrePreferenciaBuscada').keyup(function() {
        var tipoPreferenciaBuscado = $(this).val().trim();
        console.log(tipoPreferenciaBuscado);
    

        // Validar que el campo de búsqueda no esté vacío
        if (tipoPreferenciaBuscado !== '') {
            // Realizar la solicitud AJAX con jQuery
            $.ajax({
                type: 'POST',
                url: 'http://localhost/quickalive/backend/admin/gestionPreferencias/buscarPreferencia.php', // Usar una ruta relativa
                data: { tipoPreferenciaBuscado: tipoPreferenciaBuscado },
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
        $.each(resultados, function(index, preferencia) {
            var preferenciaDiv = $('<div>').addClass('preferencia');
            preferenciaDiv.append($('<span>').text('ID de preferencia: ' + preferencia.idPreferencia));
            preferenciaDiv.append($('<span>').text('Tipo de preferencia: ' + preferencia.tipoPreferencia));
    
            var editarBtn = $('<button>').addClass('editar-btn').text('Editar');
            var eliminarBtn = $('<button>').addClass('eliminar-btn').text('Eliminar');
    
            // Agregar enlaces a los botones de editar y eliminar con los parámetros de la preferencia
            editarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/gestionPreferencias/listarSubPreferencias.php?id=' + preferencia.idPreferencia + '&' + 'tipoPreferencia=' + preferencia.tipoPreferencia;
            });
    
            eliminarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/gestionPreferencias/eliminarPreferencia.php?id=' + preferencia.idPreferencia;
            });
    
            var buttonsDiv = $('<div>').addClass('preferencia-buttons');
            buttonsDiv.append(editarBtn);
            buttonsDiv.append(eliminarBtn);
    
            preferenciaDiv.append(buttonsDiv);
    
            resultadosDiv.append(preferenciaDiv);
        });
    }
});
