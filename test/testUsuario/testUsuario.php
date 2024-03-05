<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../backend/bd/bd.php";
require_once "../../backend/bd/usuario/usuario.php";

use PHPUnit\Framework\TestCase;

class TestUsuario extends TestCase
{
    private $bd;

    private function generarUsuarioAleatorio()
    {
        $nombres = ['Juan', 'María', 'Pedro', 'Ana', 'Luisa', 'Carlos'];
        $apellidos = ['García', 'López', 'Martínez', 'Fernández', 'Pérez', 'Gómez'];
        $correosDominios = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];

        // Generar datos aleatorios
        $nombre = $nombres[array_rand($nombres)];
        $apellido = $apellidos[array_rand($apellidos)];
        $telefono = mt_rand(600000000, 699999999); // Generar un número de teléfono aleatorio
        $correo = strtolower($nombre . '.' . $apellido . '@' . $correosDominios[array_rand($correosDominios)]);
        $password = password_hash('password123', PASSWORD_DEFAULT); // Contraseña fija para todos los usuarios
        $edad = mt_rand(18, 65); // Generar una edad aleatoria
        $rol = 'registrado';

        // Generar un nombre de usuario único
        $nickName = strtolower(substr($nombre, 0, 1) . $apellido . mt_rand(1, 9999));

        // Devolver un array asociativo con los datos del usuario generado
        return [
            'nickName' => $nickName,
            'telefono' => $telefono,
            'correo' => $correo,
            'password' => $password,
            'nombre' => $nombre,
            'apellidos' => $apellido,
            'edad' => $edad,
            'rol' => $rol,
        ];
    }

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

    public function testInsertarTokenRecuperacion()
    {
        // Datos de prueba
        $nickName = 'prueba';
        $correo = 'prueba@gmail.com';
        $tokenRecuperacion = $this->bd->generarTokenRecuperacion();

        // Ejecutar la función que se está probando
        $resultado = $this->bd->insertarTokenRecuperacion($nickName, $tokenRecuperacion);

        // Verificar si la inserción fue exitosa
        $this->assertTrue($resultado);

        // Verificar que el token se haya insertado correctamente en la base de datos
        $tokenRecuperacionObtenido = $this->bd->getTokenRecuperacion($correo);
        $this->assertEquals($tokenRecuperacion, $tokenRecuperacionObtenido);
    }

    public function testGetUsuarioPorIdExistente()
    {
        // Id de usuario existente en la base de datos
        $idUsuarioExistente = 12;

        // Ejecutar la función que se está probando
        $usuario = $this->bd->getUsuarioPorId($idUsuarioExistente);

        // Verificar si se obtuvo un usuario
        $this->assertInstanceOf(Usuario::class, $usuario);

        // Verificar que el usuario obtenido tenga el Id correcto
        $this->assertEquals($idUsuarioExistente, $usuario->getIdUsuario());
    }

    public function testGetUsuarioPorIdNoExistente()
    {
        // Id de usuario no existente en la base de datos
        $idUsuarioNoExistente = 9999;

        // Ejecutar la función que se está probando
        $usuario = $this->bd->getUsuarioPorId($idUsuarioNoExistente);

        // Verificar que no se haya obtenido ningún usuario
        $this->assertNull($usuario);
    }

    public function testGetUsuarioPorCorreoExistente()
    {
        // Correo electrónico existente en la base de datos
        $correoExistente = 'prueba@gmail.com';

        // Ejecutar la función que se está probando
        $usuario = $this->bd->getUsuarioPorCorreo($correoExistente);

        // Verificar si se obtuvo un usuario
        $this->assertInstanceOf(Usuario::class, $usuario);

        // Verificar que el usuario obtenido tenga el correo electrónico correcto
        $this->assertEquals($correoExistente, $usuario->getCorreo());
    }

    public function testGetUsuarioPorCorreoNoExistente()
    {
        // Correo electrónico no existente en la base de datos
        $correoNoExistente = 'correonoesxistente@example.com';

        // Ejecutar la función que se está probando
        $usuario = $this->bd->getUsuarioPorCorreo($correoNoExistente);

        // Verificar que no se haya obtenido ningún usuario
        $this->assertNull($usuario);
    }

    public function testGetUsuarioExistente()
    {
        // Nickname existente en la base de datos
        $nickNameExistente = 'prueba';

        // Ejecutar la función que se está probando
        $usuario = $this->bd->getUsuario($nickNameExistente);

        // Verificar si se obtuvo un usuario
        $this->assertInstanceOf(Usuario::class, $usuario);

        // Verificar que el usuario obtenido tenga el nickName correcto
        $this->assertEquals($nickNameExistente, $usuario->getNickName());
    }

    public function testGetUsuarioNoExistente()
    {
        // Nickname no existente en la base de datos
        $nickNameNoExistente = 'usuarionoexistente';

        // Ejecutar la función que se está probando
        $usuario = $this->bd->getUsuario($nickNameNoExistente);

        // Verificar que no se haya obtenido ningún usuario
        $this->assertNull($usuario);
    }

    public function testInsertarUsuario()
    {
        // Generar datos aleatorios para el nuevo usuario
        $usuario = $this->generarUsuarioAleatorio();

        // Ejecutar la función que se está probando
        $resultado = $this->bd->insertarUsuario(
            $usuario['nickName'],
            $usuario['telefono'],
            $usuario['correo'],
            $usuario['password'],
            $usuario['nombre'],
            $usuario['apellidos'],
            $usuario['edad'],
            $usuario['rol']
        );

        // Verificar si la inserción fue exitosa
        $this->assertTrue($resultado);

        // Verificar si el usuario insertado existe en la base de datos
        $usuarioInsertado = $this->bd->getUsuario($usuario['nickName']);
        $this->assertInstanceOf(Usuario::class, $usuarioInsertado);
        $this->assertEquals($usuario['nickName'], $usuarioInsertado->getNickName());

        // Eliminar el usuario insertado para limpiar la base de datos después de la prueba
        $this->bd->eliminarUsuario($usuarioInsertado->getIdUsuario());
    }

    public function testModificarUsuario()
    {
        // Generar un usuario aleatorio para la inserción
        $usuarioInsercion = $this->generarUsuarioAleatorio();

        // Insertar el usuario aleatorio en la base de datos
        $this->bd->insertarUsuario(
            $usuarioInsercion['nickName'],
            $usuarioInsercion['telefono'],
            $usuarioInsercion['correo'],
            $usuarioInsercion['password'],
            $usuarioInsercion['nombre'],
            $usuarioInsercion['apellidos'],
            $usuarioInsercion['edad'],
            $usuarioInsercion['rol']
        );

        // Obtener el usuario insertado
        $usuarioInsertado = $this->bd->getUsuario($usuarioInsercion['nickName']);
        $idUsuario = $usuarioInsertado->getIdUsuario();

        // Generar un usuario aleatorio para la modificación
        $usuarioModificacion = $this->generarUsuarioAleatorio();

        // Ejecutar la función que se está probando
        $resultado = $this->bd->modificarUsuario(
            $idUsuario,
            $usuarioModificacion['nickName'],
            $usuarioModificacion['telefono'],
            $usuarioModificacion['correo'],
            $usuarioModificacion['password'],
            $usuarioModificacion['nombre'],
            $usuarioModificacion['apellidos'],
            $usuarioModificacion['edad'],
            $usuarioModificacion['rol']
        );

        // Verificar si la modificación fue exitosa
        $this->assertTrue($resultado);

        // Verificar si los datos del usuario han sido modificados correctamente
        $usuarioModificado = $this->bd->getUsuarioPorId($idUsuario);

        $this->assertEquals($usuarioModificacion['nickName'], $usuarioModificado->getNickName());
        $this->assertEquals($usuarioModificacion['telefono'], $usuarioModificado->getTelefono());
        $this->assertEquals($usuarioModificacion['correo'], $usuarioModificado->getCorreo());
        $this->assertEquals($usuarioModificacion['nombre'], $usuarioModificado->getNombre());
        $this->assertEquals($usuarioModificacion['apellidos'], $usuarioModificado->getApellidos());
        $this->assertEquals($usuarioModificacion['edad'], $usuarioModificado->getEdad());
        $this->assertEquals($usuarioModificacion['rol'], $usuarioModificado->getRol());

        // Eliminar el usuario insertado para limpiar la base de datos después de la prueba
        $this->bd->eliminarUsuario($idUsuario);
    }

    public function testModificarContraseñaUsuario()
    {
        // Generar un usuario aleatorio para la inserción
        $usuario = $this->generarUsuarioAleatorio();

        // Insertar el usuario aleatorio en la base de datos
        $this->bd->insertarUsuario(
            $usuario['nickName'],
            $usuario['telefono'],
            $usuario['correo'],
            $usuario['password'],
            $usuario['nombre'],
            $usuario['apellidos'],
            $usuario['edad'],
            $usuario['rol']
        );

        // Nueva contraseña para modificar el usuario
        $nuevaContraseña = 'newpassword456';

        // Ejecutar la función que se está probando
        $resultado = $this->bd->modificarContraseñaUsuario($usuario['correo'], $nuevaContraseña);

        // Verificar si la modificación fue exitosa
        $this->assertTrue($resultado);

        // Obtener el usuario después de modificar la contraseña
        $usuarioModificado = $this->bd->getUsuarioPorCorreo($usuario['correo']);

        // Verificar si la contraseña del usuario ha sido modificada correctamente
        $this->assertTrue(password_verify($nuevaContraseña, $usuarioModificado->getPassword()));

        // Eliminar el usuario insertado para limpiar la base de datos después de la prueba
        $this->bd->eliminarUsuario($usuarioModificado->getIdUsuario());
    }

    public function testEliminarUsuario()
    {
        // Generar un usuario aleatorio para la inserción
        $usuario = $this->generarUsuarioAleatorio();

        // Insertar el usuario aleatorio en la base de datos
        $this->bd->insertarUsuario(
            $usuario['nickName'],
            $usuario['telefono'],
            $usuario['correo'],
            $usuario['password'],
            $usuario['nombre'],
            $usuario['apellidos'],
            $usuario['edad'],
            $usuario['rol']
        );

        // Obtener el ID del usuario insertado
        $usuarioInsertado = $this->bd->getUsuario($usuario['nickName']);
        $idUsuario = $usuarioInsertado->getIdUsuario();

        // Ejecutar la función que se está probando
        $resultado = $this->bd->eliminarUsuario($idUsuario);

        // Verificar si la eliminación fue exitosa
        $this->assertTrue($resultado);

        // Verificar que el usuario ya no existe en la base de datos
        $usuarioEliminado = $this->bd->getUsuario($usuario['nickName']);
        $this->assertNull($usuarioEliminado);
    }

    public function testGetRol()
    {
        // NickName del usuario existente
        $nickName = 'prueba';

        // Rol esperado del usuario
        $rolEsperado = 'registrado'; // Supongamos que el rol esperado es 1

        // Ejecutar la función que se está probando
        $rolObtenido = $this->bd->getRol($nickName);

        // Verificar si se obtiene el rol esperado
        $this->assertEquals($rolEsperado, $rolObtenido);
    }

    public function testCheckLogin()
    {
        // Datos de un usuario existente en la base de datos
        $nickName = 'prueba';
        $password = '1234'; // Supongamos que esta es la contraseña correcta

        // Ejecutar la función que se está probando
        $resultado = $this->bd->checkLogin($nickName, $password);

        // Verificar si el login fue exitoso
        $this->assertTrue($resultado);
    }

    public function testBuscarCoincidenciasUsuario()
    {
        // Insertar usuarios de prueba aleatorios
        $usuario1 = $this->bd->getUsuario('prueba');

        // Construir un array asociativo con los campos específicos
        $usuarioArray = [
            'idUsuario' => $usuario1->getIdUsuario(),
            'nickName' => $usuario1->getNickName(),
            'correo' => $usuario1->getCorreo(),
            'nombre' => $usuario1->getNombre(),
            'apellidos' => $usuario1->getApellidos(),
        ];

        // Buscar coincidencias para el nickName 'prueba'
        $resultados = $this->bd->buscarCoincidenciasUsuario('prueba');

        // Verificar que se encuentren usuarios con el nickName 'prueba'
        $this->assertNotEmpty($resultados);

        // Verificar que se encuentren los usuarios esperados en los resultados
        $this->assertEquals(1, count($resultados)); // Se espera encontrar 1 usuario con el nickName 'prueba'

        // Verificar que los usuarios encontrados coinciden con los esperados
        $this->assertContains($usuarioArray, $resultados);
    }

}
