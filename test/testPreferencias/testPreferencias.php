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
        $this->bd->iniciarConexion();
    }

// Método para limpiar el entorno de pruebas
    protected function tearDown(): void
    {
        // Aquí puedes limpiar los recursos o deshacer cambios después de cada prueba
        $this->bd->cerrarConexion();
    }

    public function testInsertarTipoDePreferencia()
    {
        // Tipo de preferencia a insertar
        $tipoPreferencia = 'Preuba';

        // Ejecutar la función que se está probando
        $idPreferenciaPadre = $this->bd->insertarTipoDePreferencia($tipoPreferencia);

        // Verificar si la inserción fue exitosa
        $this->assertNotFalse($idPreferenciaPadre);

        // Verificar si se devolvió un ID de preferencia válido
        $this->assertGreaterThan(0, $idPreferenciaPadre);

        // Eliminar la preferencia insertada
        $this->bd->eliminarTipoPreferencia($idPreferenciaPadre);
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

    public function testInsertarPreferencia()
    {
        // Insertar una preferencia padre para asociar la preferencia específica
        $tipoPreferencia = "prueba"; // Tipo de preferencia
        $idTipoPreferencia = $this->bd->insertarTipoDePreferencia($tipoPreferencia);

        // Verificar que la inserción de la preferencia padre fue exitosa
        $this->assertGreaterThan(0, $idTipoPreferencia);

        // Datos de la preferencia específica
        $nombrePreferencia = "Preferencia de prueba";

        // Ejecutar la función que se está probando
        $idPreferencia = $this->bd->insertarPreferencia($idTipoPreferencia, $tipoPreferencia, $nombrePreferencia);

        // Verificar si la inserción de la preferencia específica fue exitosa
        $this->assertGreaterThan(0, $idPreferencia);

        // Eliminar la preferencia de prueba
        $this->assertTrue($this->bd->eliminarTipoPreferencia($idTipoPreferencia));
    }

    public function testEliminarPreferencia()
    {
        // Insertar una preferencia padre para asociar la preferencia específica
        $tipoPreferencia = "Prueba";
        $idTipoPreferencia = $this->bd->insertarTipoDePreferencia($tipoPreferencia);

        // Verificar que la inserción de la preferencia padre fue exitosa
        $this->assertGreaterThan(0, $idTipoPreferencia);

        // Insertar una preferencia específica asociada a la preferencia padre
        $nombrePreferencia = "Preferencia de prueba";
        $idPreferencia = $this->bd->insertarPreferencia($idTipoPreferencia, $tipoPreferencia, $nombrePreferencia);

        // Verificar que la inserción de la preferencia específica fue exitosa
        $this->assertGreaterThan(0, $idPreferencia);

        // Ejecutar la función que se está probando
        $resultado = $this->bd->eliminarPreferencia($idPreferencia, $tipoPreferencia);

        // Verificar si la eliminación de la preferencia fue exitosa
        $this->assertTrue($resultado);

        // Eliminar la preferencia de prueba
        $this->assertTrue($this->bd->eliminarTipoPreferencia($idTipoPreferencia));

    }

    public function testBuscarCoincidenciasTipoPreferencias()
    {
        // Insertar algunos tipos de preferencia de prueba
        $tipoPreferencia1 = 'Prueba1';
        $idTipoPreferencia1 = $this->bd->insertarTipoDePreferencia($tipoPreferencia1);

        $tipoPreferencia2 = 'Prueba2';
        $idTipoPreferencia2 = $this->bd->insertarTipoDePreferencia($tipoPreferencia2);

        // Buscar coincidencias para el tipo de preferencia 'Prueba'
        $resultados = $this->bd->buscarCoincidenciasTipoPreferencias('Prueba');

        // Verificar que se encuentren tipos de preferencia con el nombre 'Prueba'
        $this->assertNotEmpty($resultados);

        // Verificar si se encuentran los tipos de preferencia esperados en los resultados
        $this->assertEquals(2, count($resultados)); // Se esperan encontrar 2 tipos de preferencia con el nombre 'Prueba'

        // Verificar que los datos de los tipos de preferencia encontrados coinciden con los esperados
        $tipoPreferencia1Encontrado = $this->bd->getTipoPreferencia($tipoPreferencia1);
        $tipoPreferencia2Encontrado = $this->bd->getTipoPreferencia($tipoPreferencia2);

        $this->assertContains($tipoPreferencia1Encontrado, $resultados);
        $this->assertContains($tipoPreferencia2Encontrado, $resultados);

        // Eliminar los tipos de preferencia de prueba
        $this->assertTrue($this->bd->eliminarTipoPreferencia($idTipoPreferencia1));
        $this->assertTrue($this->bd->eliminarTipoPreferencia($idTipoPreferencia2));
    }

    public function testObtenerPreferencias()
    {
        // Insertar un tipo de preferencia de prueba
        $tipoPreferencia = "Prueba";
        $idTipoPreferencia = $this->bd->insertarTipoDePreferencia($tipoPreferencia);

        // Insertar algunas preferencias hijas asociadas al tipo de preferencia de prueba
        $preferencias = array(
            'Preferencia 1',
            'Preferencia 2',
            'Preferencia 3',
        );

        // Insertar preferencias hijas y almacenar sus IDs en un array
        $idsPreferencias = array();
        foreach ($preferencias as $pref) {
            $idPreferencia = $this->bd->insertarPreferencia($idTipoPreferencia, $tipoPreferencia, $pref);
            $idsPreferencias[] = $idPreferencia;
        }

        // Obtener las preferencias asociadas al tipo de preferencia de prueba
        $resultado = $this->bd->obtenerPreferencias($idTipoPreferencia, $tipoPreferencia);

        // Verificar que se obtengan resultados
        $this->assertNotEmpty($resultado);

        // Verificar la cantidad de preferencias obtenidas
        $this->assertCount(count($preferencias), $resultado);

        // Verificar que los nombres de las preferencias coincidan
        foreach ($resultado as $index => $pref) {
            $this->assertEquals($preferencias[$index], $pref['nombre']);
        }

        // Limpiar el estado de la base de datos eliminando las preferencias hijas y el tipo de preferencia de prueba
        foreach ($idsPreferencias as $idPreferencia) {
            $this->assertTrue($this->bd->eliminarPreferencia($idPreferencia, $tipoPreferencia));
        }
        $this->assertTrue($this->bd->eliminarTipoPreferencia($idTipoPreferencia));
    }
}
