$(document).ready(function() {
    $('#nickNameBuscado').on('input', function() {
        var nickNameBuscado = $(this).val();

        // Realizar la solicitud AJAX con jQuery
        $.ajax({
            type: 'POST',
            url: 'http://localhost/quickalive/backend/admin/panelAdmin.php',
            data: { nickNameBuscado: nickNameBuscado },
            dataType: 'json',
            success: function(resultados) {
                mostrarResultados(resultados);
            }
        });
    });

    function mostrarResultados(resultados) {
        var resultadosDiv = $('#resultadosBusqueda');
        resultadosDiv.empty();

        // Iterar sobre los resultados y mostrarlos en el div
        $.each(resultados, function(index, usuario) {
            var usuarioDiv = $('<div>').text(usuario.nickName); // Puedes mostrar otros detalles según tus necesidades
            resultadosDiv.append(usuarioDiv);
        });
    }
});