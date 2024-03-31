<?php
// URL de la página
$url = 'https://www.ticketmaster.es/city/granada/160582';

// Obtener el contenido HTML de la página
$html = file_get_contents($url);

// Imprimir el HTML
echo $html;
