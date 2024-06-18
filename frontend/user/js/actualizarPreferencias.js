$(document).ready(function() {
    $('.preference').on('click', function() {
        $(this).toggleClass('selected');
    });

    $('#actualizarPreferencias').on('click', function() {
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
            url: 'actualizarPreferencias.php',
            data: {
                idUsuario: userId,
                preferencias: selectedPreferences
            },
            success: function(response) {
                window.location.href = '../../../backend/user/pantallaInicial.php';
            },
            error: function() {
                alert('Error al guardar preferencias.');
            }
        });
    });
});
