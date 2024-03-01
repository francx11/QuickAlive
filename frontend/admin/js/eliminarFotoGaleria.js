$(document).ready(function() {
    // Esta función se ejecuta cuando el documento HTML ha sido completamente cargado
    $(".eliminar-imagen").click(function() {
        // Obtener el ID de la imagen a eliminar del atributo data-id del botón
        var imagenId = $(this).data("id");

        // Realizar una solicitud AJAX para eliminar la imagen
        $.ajax({
            url: 'http://localhost/quickalive/backend/admin/gestionActividades/eliminarFotoGaleria.php', // URL del script PHP para eliminar la imagen
            method: "POST", // Método de la solicitud AJAX
            data: { imagenId: imagenId }, // Datos a enviar al servidor, en este caso, el ID de la imagen
            success: function(response) {
                // Función que se ejecuta si la solicitud AJAX se realiza con éxito
                // Manejar la respuesta del servidor (en este caso, simplemente imprimirla en la consola)
                console.log(response);
            },
            error: function(xhr, status, error) {
                // Función que se ejecuta si la solicitud AJAX falla
                // Manejar errores de la solicitud (en este caso, simplemente imprimir el error en la consola)
                console.error(error);
            }
        });
    });
});
