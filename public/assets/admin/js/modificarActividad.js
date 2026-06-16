$(document).ready(function() {
    // Evento submit para el formulario
    $('form').submit(function(event) {
        event.preventDefault(); // Evitar el envío del formulario por defecto
        
        // Obtener el idActividad del formulario
        var idActividad = $("#idActividad").val();

        // Crear un nuevo objeto FormData
        var formData = new FormData();

        // Agregar el idActividad al formData
        formData.append('idActividad', idActividad);

        // Agregar los campos de nombre, descripción y duración al formData
        formData.append('nombre', $("#nombre").val());
        formData.append('descripcion', $("#descripcion").val());
        formData.append('duracion', $("#duracion").val());

        // Obtener las preferencias seleccionadas
        var preferenciasSeleccionadas = [];
        $("input[name='preferencias[]']:checked").each(function() {
            var idTipoPreferencia = $(this).val();
            var tipoPreferencia = $(this).data("nombre");
            preferenciasSeleccionadas.push({
                idTipoPreferencia: idTipoPreferencia,
                tipoPreferencia: tipoPreferencia
            });
        });

        // Agregar las preferencias seleccionadas al formData como un JSON
        formData.append("preferencias", JSON.stringify(preferenciasSeleccionadas));

        // Obtener los archivos de imágenes seleccionados
        var imagenes = $("#imagenes")[0].files;

        // Agregar cada archivo de imagen al formData
        for (var i = 0; i < imagenes.length; i++) {
            formData.append("imagenes[]", imagenes[i]);
        }

        console.log(formData);

        // Realizar la solicitud AJAX para enviar los datos del formulario
        $.ajax({
            url: "http://localhost/quickalive/backend/admin/gestionActividades/modificarActividad.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Manejar la respuesta del servidor si es necesario
                console.log("Datos del formulario y preferencias guardadas exitosamente");

                console.log(formData);
                // Redirigir a otra página después de guardar los datos
                window.location.href = 'gestionActividades.php';
            },
            error: function(xhr, status, error) {
                // Manejar errores de la solicitud AJAX si es necesario
                console.error("Error al guardar los datos del formulario y las preferencias:", error);
            }
        });
    });
});

