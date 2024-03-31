$(document).ready(function() {
    // Esta función se ejecuta cuando el documento HTML ha sido completamente cargado

    // Escuchar eventos de teclado en el campo de entrada 'nickNameBuscado'
    $('#nickNameBuscado').keyup(function() {
        // Obtener el valor del campo de entrada y eliminar espacios en blanco al inicio y al final
        var nickNameBuscado = $(this).val().trim();

        // Imprimir el valor del campo de búsqueda en la consola
        console.log(nickNameBuscado);

        // Validar que el campo de búsqueda no esté vacío
        if (nickNameBuscado !== '') {
            // Realizar una solicitud AJAX utilizando jQuery
            $.ajax({
                type: 'POST', // Método de la solicitud AJAX
                url: 'buscarUsuario.php', // URL del script PHP para buscar usuarios
                data: { nickNameBuscado: nickNameBuscado }, // Datos a enviar al servidor (nombre de usuario buscado)
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
        $.each(resultados, function(index, usuario) {
            // Crear un nuevo div para cada usuario encontrado
            var usuarioDiv = $('<div>').addClass('usuario');
            // Agregar información del usuario al div
            usuarioDiv.append($('<span>').text('Id de usuario: ' + usuario.idUsuario));
            usuarioDiv.append($('<span>').text('Nombre de usuario: ' + usuario.nickName));
            usuarioDiv.append($('<span>').text('Email: ' + usuario.correo));
            usuarioDiv.append($('<span>').text('Nombre: ' + usuario.nombre));
            usuarioDiv.append($('<span>').text('Apellidos: ' + usuario.apellidos));

            // Crear botones de editar y eliminar
            var editarBtn = $('<button>').addClass('editar-btn').text('Editar');
            var eliminarBtn = $('<button>').addClass('eliminar-btn').text('Eliminar');

            // Agregar enlaces a los botones de editar y eliminar con los parámetros del usuario
            editarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/gestionUsuarios/modificarUsuario.php?id=' + usuario.idUsuario;
            });

            eliminarBtn.click(function() {
                window.location.href = 'http://localhost/quickalive/backend/admin/gestionUsuarios/eliminarUsuario.php?id=' + usuario.idUsuario;
            });

            // Crear un div para los botones y agregar los botones al div
            var buttonsDiv = $('<div>').addClass('usuario-buttons');
            buttonsDiv.append(editarBtn);
            buttonsDiv.append(eliminarBtn);

            // Agregar el div de botones al div de usuario
            usuarioDiv.append(buttonsDiv);

            // Agregar el div de usuario al contenedor de resultados
            resultadosDiv.append(usuarioDiv);
        });
    }
});
