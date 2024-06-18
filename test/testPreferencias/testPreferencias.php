<?php
require_once "../../backend/bd/bd.php";

use PHPUnit\Framework\TestCase;

class TestPreferencias extends TestCase
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

    public function testInsertarTipoDePreferencia()
    {
        // Tipo de preferencia a insertar
        $tipoPreferencia = 'Prueba';

        // Ejecutar la función que se está probando
        $idPreferenciaPadre = $this->bd->insertarTipoDePreferencia($tipoPreferencia);

        // Verificar si la inserción fue exitosa
        $this->assertNotFalse($idPreferenciaPadre);

        // Verificar si se devolvió un ID de preferencia válido
        $this->assertGreaterThan(0, $idPreferenciaPadre);

        // Eliminar la preferencia insertada
        $this->bd->eliminarTipoPreferencia($idPreferenciaPadre);
    }

    public function testObtenerNombreTipoPreferencia()
    {
        // Datos de prueba
        $nombreTipoPreferencia = 'Deporte'; // Nombre del tipo de preferencia a insertar

        // Insertar el tipo de preferencia de prueba utilizando la función `insertarTipoDePreferencia`
        $idTipoPreferencia = $this->bd->insertarTipoDePreferencia($nombreTipoPreferencia);

        // Verificar que la inserción fue exitosa y se devolvió un ID válido
        $this->assertNotFalse($idTipoPreferencia, 'La inserción del tipo de preferencia falló');
        $this->assertGreaterThan(0, $idTipoPreferencia, 'El ID del tipo de preferencia debería ser mayor que 0');

        // Ejecutar la función que se está probando
        $nombreObtenido = $this->bd->obtenerNombreTipoPreferencia($idTipoPreferencia);

        // Verificar si el nombre del tipo de preferencia se obtuvo correctamente
        $this->assertEquals($nombreTipoPreferencia, $nombreObtenido, 'El nombre del tipo de preferencia no coincide con el esperado');

        // Limpiar la base de datos después de la prueba eliminando el tipo de preferencia insertado
        $queryDelete = "DELETE FROM tipoPreferencias WHERE idTipoPreferencia = ?";
        $stmtDelete = $this->bd->mysqli->prepare($queryDelete);
        $stmtDelete->bind_param('i', $idTipoPreferencia);
        $stmtDelete->execute();
    }

    public function testEliminarTipoPreferencia()
    {
        // Insertar una preferencia para luego eliminarla
        $tipoPreferencia = "Prueba";
        $idPreferenciaPadre = $this->bd->insertarTipoDePreferencia($tipoPreferencia);

        // Verificar que la inserción fue exitosa
        $this->assertGreaterThan(0, $idPreferenciaPadre);

        // Ejecutar la función que se está probando
        $resultado = $this->bd->eliminarTipoPreferencia($idPreferenciaPadre);

        // Verificar si la eliminación fue exitosa
        $this->assertTrue($resultado);
    }

    public function testBuscarCoincidenciasTipoPreferencias()
    {
        // Datos de prueba
        $nombreTipoPreferencia1 = 'Deporte';
        $nombreTipoPreferencia2 = 'Deportes Acuáticos';
        $nombreTipoPreferencia3 = 'Deportes de Montaña';

        // Insertar los tipos de preferencia de prueba utilizando la función `insertarTipoDePreferencia`
        $idTipoPreferencia1 = $this->bd->insertarTipoDePreferencia($nombreTipoPreferencia1);
        $idTipoPreferencia2 = $this->bd->insertarTipoDePreferencia($nombreTipoPreferencia2);
        $idTipoPreferencia3 = $this->bd->insertarTipoDePreferencia($nombreTipoPreferencia3);

        // Verificar que la inserción fue exitosa y se devolvió un ID válido
        $this->assertNotFalse($idTipoPreferencia1, 'La inserción del tipo de preferencia falló');
        $this->assertNotFalse($idTipoPreferencia2, 'La inserción del tipo de preferencia falló');
        $this->assertNotFalse($idTipoPreferencia3, 'La inserción del tipo de preferencia falló');
        $this->assertGreaterThan(0, $idTipoPreferencia1, 'El ID del tipo de preferencia debería ser mayor que 0');
        $this->assertGreaterThan(0, $idTipoPreferencia2, 'El ID del tipo de preferencia debería ser mayor que 0');
        $this->assertGreaterThan(0, $idTipoPreferencia3, 'El ID del tipo de preferencia debería ser mayor que 0');

        // Ejecutar la función que se está probando
        $coincidencias = $this->bd->buscarCoincidenciasTipoPreferencias('Deporte');

        // Verificar que se encontraron coincidencias
        $this->assertNotEmpty($coincidencias, 'No se encontraron coincidencias');

        // Verificar que las coincidencias son correctas
        $preferenciasEsperadas = [
            ['idTipoPreferencia' => $idTipoPreferencia1, 'tipoPreferencia' => $nombreTipoPreferencia1],
            ['idTipoPreferencia' => $idTipoPreferencia2, 'tipoPreferencia' => $nombreTipoPreferencia2],
            ['idTipoPreferencia' => $idTipoPreferencia3, 'tipoPreferencia' => $nombreTipoPreferencia3],
        ];

        foreach ($preferenciasEsperadas as $preferenciaEsperada) {
            $this->assertContains($preferenciaEsperada, $coincidencias, 'La coincidencia esperada no se encontró');
        }

        // Limpiar la base de datos después de la prueba eliminando los tipos de preferencia insertados
        $queryDelete = "DELETE FROM tipoPreferencias WHERE idTipoPreferencia IN (?, ?, ?)";
        $stmtDelete = $this->bd->mysqli->prepare($queryDelete);
        $stmtDelete->bind_param('iii', $idTipoPreferencia1, $idTipoPreferencia2, $idTipoPreferencia3);
        $stmtDelete->execute();
    }

    public function testGetCategoriasActividad()
    {
        // Datos de prueba
        $nombreActividad = 'Actividad de prueba';
        $descripcion = 'Descripción de prueba';
        $duracion = 30;

        // Insertar una actividad de prueba
        $idActividad = $this->bd->insertarActividadSimple($nombreActividad, $descripcion, $duracion);
        $this->assertNotFalse($idActividad, 'La inserción de la actividad falló');
        $this->assertGreaterThan(0, $idActividad, 'El ID de la actividad debería ser mayor que 0');

        // Datos de categorías de prueba
        $categorias = [
            ['idTipoPreferencia' => 329],
            ['idTipoPreferencia' => 330]
        ];

        // Insertar las categorías para la actividad
        $insercionCategorias = $this->bd->insertarActividadConCategorias($idActividad, $categorias);
        $this->assertTrue($insercionCategorias, 'La inserción de categorías falló');

        // Ejecutar la función que se está probando
        $categoriasObtenidas = $this->bd->getCategoriasActividad($idActividad);

        // Verificar si se encontraron categorías
        $this->assertNotEmpty($categoriasObtenidas, 'No se encontraron categorías para la actividad');

        // Verificar que las categorías obtenidas son correctas
        foreach ($categorias as $categoriaEsperada) {
            $this->assertContains(
                ['idActividad' => $idActividad, 'idTipoPreferencia' => $categoriaEsperada['idTipoPreferencia']],
                $categoriasObtenidas,
                'La categoría esperada no se encontró en los resultados obtenidos'
            );
        }

        // Limpiar la base de datos después de la prueba
        $this->bd->eliminarActividad($idActividad);
    }

    public function testGetAllTipoPreferencias()
    {

        // Obtener todos los tipos de preferencia
        $resultado = $this->bd->getAllTipoPreferencias();

        // Verificar que se obtengan resultados
        $this->assertNotEmpty($resultado);
    }

    public function testInsertarYEliminarPreferenciaPersonal()
    {
        // Definir datos de prueba
        $idUsuario = 162;
        $nombrePreferencia = "Yoga";
        $idTipoPreferencia = 342;

        // Insertar preferencia personal
        $resultadoInsercion = $this->bd->insertarPreferenciaPersonal($idUsuario, $nombrePreferencia, $idTipoPreferencia);

        // Verificar que la inserción fue exitosa
        $this->assertTrue($resultadoInsercion);

        // Eliminar la preferencia personal insertada
        $resultadoEliminacion = $this->bd->eliminarPreferenciaPersonal($idUsuario, $nombrePreferencia, $idTipoPreferencia);

        // Verificar que la eliminación fue exitosa
        $this->assertTrue($resultadoEliminacion);
    }

    public function testActualizarPreferenciasPersonales()
    {
        // Definir datos de prueba
        $idUsuario = 162;
        $nombrePreferenciaInicial = "Yoga";
        $idTipoPreferenciaInicial = 342;
        $nombrePreferenciaNueva = "Películas";
        $idTipoPreferenciaNueva = 337;

        // Insertar preferencia personal inicial
        $resultadoInsercion = $this->bd->insertarPreferenciaPersonal($idUsuario, $nombrePreferenciaInicial, $idTipoPreferenciaInicial);

        // Verificar que la inserción inicial fue exitosa
        $this->assertTrue($resultadoInsercion);

        // Ejecutar la función para actualizar las preferencias personales del usuario con una nueva preferencia
        $this->bd->actualizarPreferenciasPersonales($idUsuario, array(array("nombreTipoPreferencia" => $nombrePreferenciaNueva, "idTipoPreferencia" => $idTipoPreferenciaNueva)));

        // Eliminar la preferencia personal insertada durante la actualización
        $resultadoEliminacion = $this->bd->eliminarPreferenciaPersonal($idUsuario, $nombrePreferenciaNueva, $idTipoPreferenciaNueva);

        // Verificar que la eliminación fue exitosa
        $this->assertTrue($resultadoEliminacion);
    }

    public function testEliminarPreferenciasPersonalesUsuario()
    {
        // Datos de prueba
        $idUsuario = 162; // Suponiendo que el usuario con ID 1 existe en la base de datos

        // Preparar datos: Insertar preferencias personales para el usuario de prueba
        $nombreTipoPreferencia1 = 'Preferencia 1';
        $idTipoPreferencia1 = 329; // Suponiendo que el tipo de preferencia con ID 1 existe en la base de datos
        $this->bd->insertarPreferenciaPersonal($idUsuario, $nombreTipoPreferencia1, $idTipoPreferencia1);

        $nombreTipoPreferencia2 = 'Preferencia 2';
        $idTipoPreferencia2 = 330; // Suponiendo que el tipo de preferencia con ID 2 existe en la base de datos
        $this->bd->insertarPreferenciaPersonal($idUsuario, $nombreTipoPreferencia2, $idTipoPreferencia2);

        // Ejecutar la función que se está probando
        $resultado = $this->bd->eliminarPreferenciasPersonalesUsuario($idUsuario);

        // Verificar si la eliminación fue exitosa
        $this->assertTrue($resultado, 'La eliminación de las preferencias personales del usuario falló');

        // Obtener las preferencias personales del usuario para verificar la eliminación
        $preferenciasPersonales = $this->bd->getPreferenciasUsuario($idUsuario);

        // Verificar si no se encontraron preferencias personales
        $this->assertEmpty($preferenciasPersonales, 'Las preferencias personales del usuario no fueron eliminadas');

        // Limpiar la base de datos después de la prueba (opcional)
        // En este caso, no se requiere ya que se supone que las preferencias fueron eliminadas
    }
}
