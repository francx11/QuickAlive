$(document).ready(function() {
    $('.preference').on('click', function() {
        $(this).toggleClass('selected');
    });

    $('#guardarPreferencias').on('click', function() {
        const selectedPreferences = $('.preference.selected').map(function() {
            return {
                idTipoPreferencia: $(this).data('id'),
                nombreTipoPreferencia: $(this).data('nombre')
            };
        }).get();

        const userId = $('#usuario').data('id-usuario');

        console.log(selectedPreferences);

        $.ajax({
            type: 'POST',
            url: 'guardarPreferencias.php',
            data: {
                idUsuario: userId,
                preferencias: selectedPreferences
            },
            success: function(response) {
                window.location.href = '../../../backend/api/sesiones/login.php';
            },
            error: function() {
                alert('Error al guardar preferencias.');
            }
        });
    });
});
