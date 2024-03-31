$(document).ready(function() {
    // Esta función se ejecuta cuando el documento HTML ha sido completamente cargado
    $(".eliminar-imagen").click(function() {
        // Obtener el ID de la imagen a eliminar del atributo data-id del botón
        var imagenId = $(this).data("id");

        // Guardar una referencia al botón actual para eliminar la imagen del DOM después
        var $botonEliminar = $(this);

        // Realizar una solicitud AJAX para eliminar la imagen
        $.ajax({
            url: 'eliminarFotoGaleria.php', // URL del script PHP para eliminar la imagen
            method: "POST", // Método de la solicitud AJAX
            data: { imagenId: imagenId }, // Datos a enviar al servidor, en este caso, el ID de la imagen
            success: function(response) {
                // Función que se ejecuta si la solicitud AJAX se realiza con éxito
                // Manejar la respuesta del servidor (en este caso, simplemente imprimirla en la consola)
                console.log(response);
                
                // Eliminar la imagen del DOM después de que la solicitud tenga éxito
                $botonEliminar.closest('.imagen').remove();
            },
            error: function(xhr, status, error) {
                // Función que se ejecuta si la solicitud AJAX falla
                // Manejar errores de la solicitud (en este caso, simplemente imprimir el error en la consola)
                console.error(error);
            }
        });
    });
});
