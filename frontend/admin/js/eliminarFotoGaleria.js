$(document).ready(function() {
    $(".eliminar-imagen").click(function() {
        // Obtener el ID de la imagen a eliminar
        var imagenId = $(this).data("id");

        // Realizar una solicitud AJAX para eliminar la imagen
        $.ajax({
            url: 'http://localhost/quickalive/backend/admin/gestionActividades/eliminarFotoGaleria.php',
            method: "POST",
            data: { imagenId: imagenId
                   }, // Pasar el ID de la imagen como datos POST
            success: function(response) {
                // Manejar la respuesta del servidor
                console.log(response);
            },
            error: function(xhr, status, error) {
                // Manejar errores de la solicitud (opcional)
                console.error(error);
            }
        });
    });
});
