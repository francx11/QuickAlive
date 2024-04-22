$(document).ready(function() {
    // Función para alternar la selección de preferencias al hacer clic
    $(".preferencia").click(function() {
        $(this).toggleClass("seleccionada"); // Alternar la clase "seleccionada" al hacer clic
    });

    // Evento click para el botón de guardar preferencias
    $("#actualizarPreferencias").click(function() {
        // Obtener el ID del usuario del atributo de datos del elemento HTML
        var idUsuario = $("#usuario").data("id-usuario");

        console.log(idUsuario);

        // Array para almacenar las preferencias seleccionadas
        var preferenciasSeleccionadas = [];

        
        // Recorrer todos los checkboxes de preferencias marcados
        $("input[name='preferencias[]']:checked").each(function() {
        // Obtener el nombreTipo y el idTipoPreferencia de cada checkbox
        var nombreTipoPreferencia = $(this).data("nombre");
        var idTipoPreferencia = $(this).val();

        // Agregar los datos al array de preferenciasSeleccionadas
        preferenciasSeleccionadas.push({
            nombreTipoPreferencia: nombreTipoPreferencia,
            idTipoPreferencia: idTipoPreferencia
        });
        });

        console.log(preferenciasSeleccionadas);

        // Realizar la solicitud AJAX para enviar las preferencias seleccionadas y el ID del usuario al archivo PHP
        $.ajax({
            url: "actualizarPreferencias.php",
            type: "POST",
            data: { idUsuario: idUsuario, preferencias: preferenciasSeleccionadas },
            success: function(response) {
                // Manejar la respuesta del servidor si es necesario
                console.log("Preferencias guardadas exitosamente");
                //window.location.href = '../../../backend/api/sesiones/login.php';
                //window.location.href = '../../../backend/user/gestionRegistro/registroPreferencias.php'
                // Manejar la respuesta del servidor si es necesario
                window.location.href = '../../../backend/user/pantallaInicial.php';
            },
            error: function(xhr, status, error) {
                // Manejar errores de la solicitud AJAX si es necesario
                console.error("Error al guardar las preferencias:", error);
            }
        });
    });
});
