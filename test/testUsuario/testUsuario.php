<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../backend/bd/bd.php";
require_once "../../backend/bd/usuario/usuario.php";

use PHPUnit\Framework\TestCase;

class TestUsuario extends TestCase
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
    public function testGetTokenRecuperacionExistente()
    {
        // Correo electrónico existente en la base de datos
        $correoExistente = 'franexca@gmail.com';
        // Suponiendo que haya un token asociado a este correo en la base de datos
        $tokenEsperado = '6a57afd571a24cff08f872ca9573b59d73c77cff7f8bb1e8bb6c4756364d0305';

        $tokenRecuperacion = $this->bd->getTokenRecuperacion($correoExistente);

        $this->assertEquals($tokenEsperado, $tokenRecuperacion);
    }

    public function testGetTokenRecuperacionNoExistente()
    {
        // Correo electrónico no existente en la base de datos
        $correoNoExistente = 'correo@noexistente.com';

        $tokenRecuperacion = $this->bd->getTokenRecuperacion($correoNoExistente);

        $this->assertNull($tokenRecuperacion);
    }
    // Otros métodos de prueba para otras funcionalidades de la tabla usuario

}
