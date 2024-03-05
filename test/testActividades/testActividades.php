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
        $this->bd->iniciarConexion();
    }

    // Método para limpiar el entorno de pruebas
    protected function tearDown(): void
    {
        // Aquí puedes limpiar los recursos o deshacer cambios después de cada prueba
        $this->bd->cerrarConexion();
    }

    public function testInsertarActividadSimple()
    {
        // Datos de la actividad
        $nombreActividad = 'Actividad de prueba';
        $descripcion = 'Esta es una actividad de prueba';
        $tipoActividad = 'Simple';
        $duracion = 60; // Duración en minutos

        // Ejecutar la función que se está probando
        $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $tipoActividad, $duracion);

        // Verificar si la inserción fue exitosa
        $this->assertNotFalse($idActividad);

        // Verificar si se devolvió un ID de actividad válido
        $this->assertGreaterThan(0, $idActividad);

        // Verificar si se puede obtener la actividad insertada de la base de datos
        $actividadInsertada = $this->bd->getActividad($idActividad);

        // Verificar si se encontró la actividad insertada
        $this->assertNotNull($actividadInsertada);

        // Verificar si la actividad insertada es del tipo correcto
        $this->assertInstanceOf(ActividadSimple::class, $actividadInsertada);

        // Verificar si los datos de la actividad coinciden con los esperados
        $this->assertEquals($nombreActividad, $actividadInsertada->getNombreActividad());
        $this->assertEquals($descripcion, $actividadInsertada->getDescripcion());
        $this->assertEquals($tipoActividad, $actividadInsertada->getTipoActividad());
        $this->assertEquals($duracion, $actividadInsertada->getDuracion());

        // Eliminar la actividad insertada para limpiar la base de datos después de la prueba
        $this->bd->eliminarActividad($idActividad);
    }

    public function testModificarActividad()
    {
        // Insertar una actividad de prueba para luego modificarla
        $nombreActividad = 'Actividad original';
        $descripcionOriginal = 'Esta es la descripción original de la actividad';
        $tipoActividadOriginal = 'Simple';
        $duracionOriginal = 60;

        // Insertar la actividad de prueba
        $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcionOriginal, $tipoActividadOriginal, $duracionOriginal);

        // Datos de modificación
        $nuevoNombreActividad = 'Nueva actividad';
        $nuevaDescripcion = 'Esta es una descripción modificada';
        $nuevoTipoActividad = 'Compleja';
        $nuevaDuracion = 90;

        // Ejecutar la función que se está probando
        $resultado = $this->bd->modificarActividad($idActividad, $nuevoNombreActividad, $nuevaDescripcion, $nuevoTipoActividad, $nuevaDuracion);

        // Verificar si la modificación fue exitosa
        $this->assertTrue($resultado);

        // Obtener la actividad modificada de la base de datos
        $actividadModificada = $this->bd->getActividad($idActividad);

        // Verificar si los datos de la actividad han sido modificados correctamente
        $this->assertEquals($nuevoNombreActividad, $actividadModificada->getNombreActividad());
        $this->assertEquals($nuevaDescripcion, $actividadModificada->getDescripcion());
        $this->assertEquals($nuevoTipoActividad, $actividadModificada->getTipoActividad());
        $this->assertEquals($nuevaDuracion, $actividadModificada->getDuracion());

        // Eliminar la actividad insertada después de la prueba
        $this->bd->eliminarActividad($idActividad);
    }

    public function testEliminarActividad()
    {
        // Insertar una actividad de prueba
        $nombreActividad = 'Actividad de prueba';
        $descripcion = 'Esta es una actividad de prueba';
        $tipoActividad = 'Simple';
        $duracion = 60;

        $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $tipoActividad, $duracion);

        // Insertar algunas fotos de galería asociadas a la actividad
        $numFotos = 3;
        for ($i = 1; $i <= $numFotos; $i++) {
            $nombreFoto = "foto_$i.jpg";
            $this->bd->agregarFotoGaleria($idActividad, $nombreFoto);
        }

        // Ejecutar la función que se está probando
        $resultado = $this->bd->eliminarActividad($idActividad);

        // Verificar si la eliminación fue exitosa
        $this->assertTrue($resultado);

        // Verificar que la actividad y todas las fotos de galería asociadas hayan sido eliminadas correctamente
        $actividadEliminada = $this->bd->getActividad($idActividad);
        $this->assertNull($actividadEliminada);

        $fotosGaleria = $this->bd->getGaleriaActividad($idActividad);
        $this->assertEmpty($fotosGaleria);
    }

    public function testEliminarFotoGaleria()
    {
        // Insertar una actividad de prueba
        $nombreActividad = 'Actividad de prueba';
        $descripcion = 'Esta es una actividad de prueba';
        $tipoActividad = 'Simple';
        $duracion = 60;

        $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $tipoActividad, $duracion);

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
        $tipoActividad = 'Simple';
        $duracion = 60;
        $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $tipoActividad, $duracion);

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
            'tipoActividad' => 'Simple',
            'duracion' => 60,
        );
        $idActividad1 = $this->bd->insertarActividadSimple($actividad1['nombreActividad'], $actividad1['descripcion'], $actividad1['tipoActividad'], $actividad1['duracion']);

        $actividad2 = array(
            'nombreActividad' => 'Actividad de prueba 2',
            'descripcion' => 'Esta es una actividad de prueba 2',
            'tipoActividad' => 'Compleja',
            'duracion' => 120,
        );
        $idActividad2 = $this->bd->insertarActividadSimple($actividad2['nombreActividad'], $actividad2['descripcion'], $actividad2['tipoActividad'], $actividad2['duracion']);

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
            'tipoActividad' => $actividad->getTipoActividad(),
            'duracion' => $actividad->getDuracion(),
        );
    }

    public function testGetActividad()
    {
        // Insertar una actividad de prueba
        $nombreActividad = 'Actividad de prueba';
        $descripcion = 'Esta es una actividad de prueba';
        $tipoActividad = 'Simple';
        $duracion = 60;
        $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $tipoActividad, $duracion);

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
        $tipoActividad = 'Simple';
        $duracion = 60;
        $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $tipoActividad, $duracion);

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

}
