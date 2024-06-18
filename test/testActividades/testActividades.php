<?php
require_once "../../backend/bd/bd.php";
require_once "../../backend/bd/actividad/actividad.php";

use PHPUnit\Framework\TestCase;

class TestActividades extends TestCase
{
        private $bd;

        // Método para inicializar el entorno de pruebas
        protected function setUp(): void
        {
                // Aquí puedes realizar configuraciones necesarias antes de cada prueba
                $this->bd = new BD();
        }

        // Método para limpiar el entorno de pruebas
        protected function tearDown(): void
        {
        }

        public function testInsertarActividadSimple()
        {
                // Datos de la actividad
                $nombreActividad = 'Actividad de prueba';
                $descripcion = 'Esta es una actividad de prueba';
                $duracion = 60; // Duración en minutos

                // Ejecutar la función que se está probando
                $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $duracion);

                // Verificar si la inserción fue exitosa
                $this->assertNotFalse($idActividad, 'La inserción de la actividad falló');

                // Verificar si se devolvió un ID de actividad válido
                $this->assertGreaterThan(0, $idActividad, 'ID de actividad no válido');

                // Verificar si se puede obtener la actividad insertada de la base de datos
                $actividadInsertada = $this->bd->getActividad($idActividad);

                // Verificar si se encontró la actividad insertada
                $this->assertNotNull($actividadInsertada, 'No se encontró la actividad insertada');

                // Verificar si la actividad insertada es del tipo correcto
                $this->assertInstanceOf(ActividadSimple::class, $actividadInsertada, 'La actividad insertada no es del tipo ActividadSimple');

                // Verificar si los datos de la actividad coinciden con los esperados
                $this->assertEquals($nombreActividad, $actividadInsertada->getNombreActividad(), 'El nombre de la actividad no coincide');
                $this->assertEquals($descripcion, $actividadInsertada->getDescripcion(), 'La descripción de la actividad no coincide');
                $this->assertEquals($duracion, $actividadInsertada->getDuracion(), 'La duración de la actividad no coincide');

                // Eliminar la actividad insertada para limpiar la base de datos después de la prueba
                $this->bd->eliminarActividad($idActividad);
        }


        public function testInsertarActividadGeolocalizable()
        {
                // Datos de la actividad geolocalizable
                $nombreActividad = 'Actividad de prueba geolocalizable';
                $descripcion = 'Esta es una actividad de prueba geolocalizable';
                $duracion = 90; // Duración en minutos
                $urlImagen = 'https://ejemplo.com/imagen.jpg';
                $idApi = 'abc123';
                $fechaLimite = '2024-12-31 00:00:00'; // Fecha límite en formato yyyy-mm-dd

                // Ejecutar la función que se está probando
                $idActividad = $this->bd->insertarActividadGeolocalizable($nombreActividad, $descripcion, $duracion, $urlImagen, $idApi, $fechaLimite);

                // Verificar si la inserción fue exitosa
                $this->assertNotFalse($idActividad, 'La inserción de la actividad geolocalizable falló');

                // Verificar si se devolvió un ID de actividad válido
                $this->assertGreaterThan(0, $idActividad, 'ID de actividad geolocalizable no válido');

                // Verificar si se puede obtener la actividad insertada de la base de datos
                $actividadInsertada = $this->bd->getActividadGeolocalizable($idActividad);

                // Verificar si se encontró la actividad insertada
                $this->assertNotNull($actividadInsertada, 'No se encontró la actividad geolocalizable insertada');

                // Verificar si la actividad insertada es del tipo correcto
                $this->assertInstanceOf(ActividadGeolocalizable::class, $actividadInsertada, 'La actividad insertada no es del tipo ActividadGeolocalizable');

                // Verificar si los datos de la actividad coinciden con los esperados
                $this->assertEquals($nombreActividad, $actividadInsertada->getNombreActividad(), 'El nombre de la actividad no coincide');
                $this->assertEquals($descripcion, $actividadInsertada->getDescripcion(), 'La descripción de la actividad no coincide');
                $this->assertEquals($duracion, $actividadInsertada->getDuracion(), 'La duración de la actividad no coincide');
                $this->assertEquals($urlImagen, $actividadInsertada->getUrlRemota(), 'La URL de la imagen no coincide');
                $this->assertEquals($idApi, $actividadInsertada->getIdApi(), 'El ID de API no coincide');
                $this->assertEquals($fechaLimite, $actividadInsertada->getFechaLimite(), 'La fecha límite no coincide');

                // Eliminar la actividad insertada para limpiar la base de datos después de la prueba
                $this->bd->eliminarActividad($idActividad);
        }


        public function testInsertarActividadConCategorias()
        {
                // Datos de prueba
                $nombreActividad = 'Actividad de prueba con categorías';
                $descripcion = 'Esta es una actividad de prueba con categorías';
                $duracion = 120; // Duración en minutos
                $categorias = [
                        ["idTipoPreferencia" => 329],
                        ["idTipoPreferencia" => 330],
                        ["idTipoPreferencia" => 331]
                ];

                // Insertar la actividad primero (puedes usar tu método insertarActividad aquí)
                $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $duracion);

                // Verificar si la inserción de la actividad fue exitosa
                $this->assertNotFalse($idActividad, 'La inserción de la actividad falló');

                // Insertar las categorías de la actividad
                $resultado = $this->bd->insertarActividadConCategorias($idActividad, $categorias);

                // Verificar si la inserción de categorías fue exitosa
                $this->assertTrue($resultado, 'La inserción de categorías de actividad falló');

                // Verificar si se pueden obtener las categorías insertadas para la actividad
                $categoriasInsertadas = $this->bd->getCategoriasActividad($idActividad);

                // Verificar si se encontraron categorías insertadas
                $this->assertNotEmpty($categoriasInsertadas, 'No se encontraron categorías insertadas para la actividad');

                // Verificar la cantidad de categorías insertadas
                $this->assertCount(count($categorias), $categoriasInsertadas, 'La cantidad de categorías insertadas no coincide con las esperadas');

                // Verificar que las categorías insertadas sean las correctas
                $idsCategoriasInsertadas = array_map(function ($categoria) {
                        return $categoria['idTipoPreferencia'];
                }, $categoriasInsertadas);

                foreach ($categorias as $categoria) {
                        $this->assertContains($categoria['idTipoPreferencia'], $idsCategoriasInsertadas, 'No se encontró la categoría esperada');
                }

                // Limpiar la base de datos después de la prueba
                $this->bd->eliminarActividad($idActividad);
        }


        public function testModificarActividad()
        {
                // Insertar una actividad de prueba para luego modificarla
                $nombreActividad = 'Actividad original';
                $descripcionOriginal = 'Esta es la descripción original de la actividad';
                $duracionOriginal = 60;

                // Insertar la actividad de prueba
                $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcionOriginal, $duracionOriginal);

                // Datos de modificación
                $nuevoNombreActividad = 'Nueva actividad';
                $nuevaDescripcion = 'Esta es una descripción modificada';
                $nuevaDuracion = 90;

                // Ejecutar la función que se está probando
                $resultado = $this->bd->modificarActividad($idActividad, $nuevoNombreActividad, $nuevaDescripcion, $nuevaDuracion);

                // Verificar si la modificación fue exitosa
                $this->assertTrue($resultado);

                // Obtener la actividad modificada de la base de datos
                $actividadModificada = $this->bd->getActividad($idActividad);

                // Verificar si los datos de la actividad han sido modificados correctamente
                $this->assertEquals($nuevoNombreActividad, $actividadModificada->getNombreActividad());
                $this->assertEquals($nuevaDescripcion, $actividadModificada->getDescripcion());
                $this->assertEquals($nuevaDuracion, $actividadModificada->getDuracion());

                // Eliminar la actividad insertada después de la prueba
                $this->bd->eliminarActividad($idActividad);
        }


        public function testEliminarFotoGaleria()
        {
                // Insertar una actividad de prueba
                $nombreActividad = 'Actividad de prueba';
                $descripcion = 'Esta es una actividad de prueba';
                $duracion = 60;

                $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $duracion);

                // Insertar una foto de galería asociada a la actividad
                $nombreFoto = "foto_prueba.jpg";
                // Obtener el número de la imagen insertada
                $numImagen = $this->bd->agregarFotoGaleria($idActividad, $nombreFoto);

                // Ejecutar la función que se está probando
                $resultado = $this->bd->eliminarFotoGaleria($numImagen);

                // Verificar si la eliminación fue exitosa
                $this->assertTrue($resultado);

                // Eliminar la actividad de prueba después de la prueba
                $this->bd->eliminarActividad($idActividad);
        }


        public function testAgregarFotoGaleria()
        {
                // Insertar una actividad de prueba
                $nombreActividad = 'Actividad de prueba';
                $descripcion = 'Esta es una actividad de prueba';
                $duracion = 60;
                $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $duracion);

                // URL de la foto
                $urlFoto = "ruta/imagen.jpg";

                // Ejecutar la función que se está probando
                $idFoto = $this->bd->agregarFotoGaleria($idActividad, $urlFoto);

                // Verificar si se obtuvo un ID de foto válido
                $this->assertGreaterThan(0, $idFoto);

                // Verificar si la foto fue agregada a la galería
                $this->assertNotEquals(-1, $idFoto);

                // Eliminar la actividad de prueba
                $this->bd->eliminarActividad($idActividad);
        }


        public function testBuscarCoincidenciasActividad()
        {
                // Insertar algunas actividades de prueba
                $actividad1 = array(
                        'nombreActividad' => 'Actividad de prueba 1',
                        'descripcion' => 'Esta es una actividad de prueba 1',
                        'duracion' => 60,
                );
                $idActividad1 = $this->bd->insertarActividadSimple($actividad1['nombreActividad'], $actividad1['descripcion'], $actividad1['duracion']);

                $actividad2 = array(
                        'nombreActividad' => 'Actividad de prueba 2',
                        'descripcion' => 'Esta es una actividad de prueba 2',
                        'duracion' => 120,
                );
                $idActividad2 = $this->bd->insertarActividadSimple($actividad2['nombreActividad'], $actividad2['descripcion'], $actividad2['duracion']);

                // Buscar coincidencias para el nombre de la actividad 'Actividad de prueba'
                $resultados = $this->bd->buscarCoincidenciasActividad('prueba');

                // Verificar que se encuentren actividades con el nombre 'Actividad de prueba'
                $this->assertNotEmpty($resultados);

                // Verificar si se encuentran las actividades esperadas en los resultados
                $this->assertEquals(2, count($resultados)); // Se esperan encontrar 2 actividades con el nombre 'Actividad de prueba'

                // Verificar que los datos de las actividades encontradas coinciden con los esperados
                $actividad1Encontrada = $this->bd->getActividad($idActividad1);
                $actividad2Encontrada = $this->bd->getActividad($idActividad2);

                $this->assertContains($this->toArray($actividad1Encontrada), $resultados);
                $this->assertContains($this->toArray($actividad2Encontrada), $resultados);

                // Eliminar las actividades de prueba
                $this->bd->eliminarActividad($idActividad1);
                $this->bd->eliminarActividad($idActividad2);
        }


        // Función para convertir un objeto Actividad a un array asociativo
        private function toArray($actividad)
        {
                return array(
                        'idActividad' => $actividad->getIdActividad(),
                        'nombreActividad' => $actividad->getNombreActividad(),
                        'descripcion' => $actividad->getDescripcion(),
                        'duracion' => $actividad->getDuracion(),
                );
        }


        public function testGetActividad()
        {
                // Insertar una actividad de prueba
                $nombreActividad = 'Actividad de prueba';
                $descripcion = 'Esta es una actividad de prueba';
                $tipoActividad = 'simple';
                $duracion = 60;
                $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $duracion);

                // Insertar una foto en la galería asociada a la actividad
                $nombreFoto = "foto_prueba.jpg";
                $this->bd->agregarFotoGaleria($idActividad, $nombreFoto);

                // Obtener la actividad insertada
                $actividad = $this->bd->getActividad($idActividad);

                // Verificar si la actividad se recuperó correctamente
                $this->assertNotNull($actividad);

                // Verificar si los datos de la actividad coinciden con los esperados
                $this->assertEquals($nombreActividad, $actividad->getNombreActividad());
                $this->assertEquals($descripcion, $actividad->getDescripcion());
                $this->assertEquals($tipoActividad, $actividad->getTipoActividad());
                $this->assertEquals($duracion, $actividad->getDuracion());

                // Verificar si se recuperaron las fotos de la galería asociada a la actividad
                $fotosGaleriaEsperadas = array($nombreFoto);
                $fotosGaleriaObtenidas = $actividad->getGaleriaFotos();
                $this->assertEquals($fotosGaleriaEsperadas, $fotosGaleriaObtenidas);

                // Eliminar la actividad de prueba
                $this->bd->eliminarActividad($idActividad);
        }


        public function testGetGaleriaActividad()
        {
                // Insertar una actividad de prueba
                $nombreActividad = 'Actividad de prueba';
                $descripcion = 'Esta es una actividad de prueba';
                $duracion = 60;
                $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $duracion);

                // Agregar fotos a la galería asociada a la actividad
                $numImagen1 = $this->bd->agregarFotoGaleria($idActividad, 'foto1.jpg');
                $numImagen2 = $this->bd->agregarFotoGaleria($idActividad, 'foto2.jpg');
                $numImagen3 = $this->bd->agregarFotoGaleria($idActividad, 'foto3.jpg');

                // Obtener la galería de la actividad
                $galeria = $this->bd->getGaleriaActividad($idActividad);

                // Verificar si se recuperó la galería correctamente
                $this->assertNotEmpty($galeria);
                $this->assertCount(3, $galeria);

                // Verificar si los números de imagen coinciden
                $this->assertEquals($numImagen1, $galeria[0]->getNumImagen());
                $this->assertEquals($numImagen2, $galeria[1]->getNumImagen());
                $this->assertEquals($numImagen3, $galeria[2]->getNumImagen());

                // Eliminar la actividad de prueba
                $this->bd->eliminarActividad($idActividad);
        }

        public function testObtenerActividadesArealizar()
        {
                // Datos de prueba
                $idUsuario = 162; // ID del usuario (adaptar según tu base de datos y situación de prueba)

                // Insertar una actividad de prueba que esté pendiente por realizar
                $nombreActividad = 'Actividad de prueba para obtener';
                $descripcion = 'Esta es una actividad de prueba para obtener pendiente';
                $duracion = 60; // Duración en minutos

                // Insertar la actividad de prueba
                $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, 'Simple', 'SubSimple', $duracion);

                // Verificar si la inserción de la actividad fue exitosa
                $this->assertNotFalse($idActividad, 'La inserción de la actividad falló');

                // Realizar la actividad para que quede pendiente por realizar
                $resultadoRealizar = $this->bd->realizarActividad($idUsuario, $idActividad);
                $this->assertTrue($resultadoRealizar, 'La realización de la actividad falló');

                // Ejecutar la función que se está probando
                $actividadesPendientes = $this->bd->obtenerActividadesArealizar($idUsuario);

                // Verificar si se obtuvieron actividades pendientes correctamente
                $this->assertIsArray($actividadesPendientes, 'La función no devolvió un array');
                $this->assertNotEmpty($actividadesPendientes, 'No se encontraron actividades pendientes');

                // Verificar que la actividad insertada está entre las actividades pendientes
                $actividadEncontrada = false;
                foreach ($actividadesPendientes as $actividad) {
                        if ($actividad['idActividad'] == $idActividad) {
                                $actividadEncontrada = true;
                                break;
                        }
                }
                $this->assertTrue($actividadEncontrada, 'La actividad insertada no está entre las actividades pendientes');

                // Limpiar la base de datos después de la prueba eliminando la actividad y la realización
                $this->bd->eliminarActividad($idActividad);
        }

        public function testObtenerHistorialActividades()
        {
                // Datos de prueba
                $idUsuario = 162; // ID del usuario (adaptar según tu base de datos y situación de prueba)

                // Insertar una actividad de prueba que esté completada en el historial
                $nombreActividad = 'Actividad de prueba para historial';
                $descripcion = 'Esta es una actividad de prueba para historial completada';
                $duracion = 60; // Duración en minutos

                // Insertar la actividad de prueba
                $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, 'Simple', 'SubSimple', $duracion);

                // Realizar la actividad para marcarla como completada en el historial
                $resultadoRealizar = $this->bd->realizarActividad($idUsuario, $idActividad);
                $this->assertTrue($resultadoRealizar, 'La realización de la actividad falló');

                // Marcar la actividad como completada
                $this->bd->completarActividad($idUsuario, $idActividad);

                // Ejecutar la función que se está probando
                $historialActividades = $this->bd->obtenerHistorialActividades($idUsuario);

                // Verificar si se obtuvo el historial de actividades completadas correctamente
                $this->assertIsArray($historialActividades, 'La función no devolvió un array');
                $this->assertNotEmpty($historialActividades, 'No se encontraron actividades en el historial');

                // Verificar que la actividad insertada está entre las actividades del historial
                $actividadEncontrada = false;
                foreach ($historialActividades as $actividad) {
                        if ($actividad['idActividad'] == $idActividad) {
                                $actividadEncontrada = true;
                                break;
                        }
                }
                $this->assertTrue($actividadEncontrada, 'La actividad insertada no está en el historial');

                // Limpiar la base de datos después de la prueba eliminando la actividad y el historial
                $this->bd->eliminarActividad($idActividad);
        }
}
