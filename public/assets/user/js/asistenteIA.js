$(document).ready(function () {
  function agregarMensaje(texto, clase) {
    var mensajeHTML = '<div class="mensaje ' + clase + '">' + texto + '</div>';
    $('#chat-mensajes').append(mensajeHTML);
    $('#chat-mensajes').scrollTop($('#chat-mensajes')[0].scrollHeight);
  }

  function agregarActividades(actividades) {
    if (!actividades || actividades.length === 0) {
      return;
    }

    var contenedorHTML = '<div class="mensaje asistente actividades-recomendadas">';
    actividades.forEach(function (actividad) {
      contenedorHTML += '<div class="actividad-card">';
      contenedorHTML += '<h4>' + actividad.nombreActividad + '</h4>';
      contenedorHTML += '<p>' + actividad.descripcion + '</p>';
      contenedorHTML += '<p>Duración: ' + actividad.duracion + ' minutos</p>';
      contenedorHTML += '</div>';
    });
    contenedorHTML += '</div>';

    $('#chat-mensajes').append(contenedorHTML);
    $('#chat-mensajes').scrollTop($('#chat-mensajes')[0].scrollHeight);
  }

  $('#chat-form').on('submit', function (event) {
    event.preventDefault();

    var mensaje = $('#chat-input').val().trim();
    if (mensaje === '') {
      return;
    }

    agregarMensaje(mensaje, 'usuario');
    $('#chat-input').val('');

    $.ajax({
      url: 'http://localhost/quickalive/backend/user/gestionAsistenteIA/procesarMensajeChat.php',
      type: 'POST',
      data: { mensaje: mensaje },
      dataType: 'json',
      success: function (response) {
        if (response.error) {
          agregarMensaje(response.error, 'error');
          return;
        }
        agregarMensaje(response.respuesta, 'asistente');
        agregarActividades(response.actividadesRecomendadas);
      },
      error: function () {
        agregarMensaje('Error al contactar con el asistente IA.', 'error');
      },
    });
  });
});
