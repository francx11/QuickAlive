$(document).ready(function() {
    $('#nickNameBuscado').keyup( function() {
        var nickNameBuscado = $(this).val().trim();
        console.log(nickNameBuscado);

        // Validar que el campo de búsqueda no esté vacío
        if (nickNameBuscado !== '') {
            // Realizar la solicitud AJAX con jQuery
            $.ajax({
                type: 'POST',
                url: 'http://localhost/quickalive/backend/admin/buscarUsuario.php', // Usar una ruta relativa
                data: { nickNameBuscado: nickNameBuscado },
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
        $.each(resultados, function(index, usuario) {
            var usuarioDiv = $('<div>').addClass('usuario');
            usuarioDiv.append($('<span>').text('Id de usuario: ' + usuario.idUsuario));
            usuarioDiv.append($('<span>').text('Nombre de usuario: ' + usuario.nickName));
            usuarioDiv.append($('<span>').text('Email: ' + usuario.email));
            usuarioDiv.append($('<span>').text('Nombre: ' + usuario.nombre));
            usuarioDiv.append($('<span>').text('Apellidos: ' + usuario.apellidos));
    
            var editarBtn = $('<button>').addClass('editar-btn').text('Editar');
            var eliminarBtn = $('<button>').addClass('eliminar-btn').text('Eliminar');
    
            // Agregar enlaces a los botones de editar y eliminar con los parámetros del usuario
            editarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/modificarUsuario.php?id=' + usuario.idUsuario;
            });
    
            eliminarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/eliminarUsuario.php?id=' + usuario.idUsuario;
            });
    
            var buttonsDiv = $('<div>').addClass('usuario-buttons');
            buttonsDiv.append(editarBtn);
            buttonsDiv.append(eliminarBtn);
    
            usuarioDiv.append(buttonsDiv);
    
            resultadosDiv.append(usuarioDiv);
        });
    }
    


    


    
});
