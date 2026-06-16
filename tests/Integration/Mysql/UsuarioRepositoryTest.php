<?php

declare(strict_types=1);

namespace Tests\Integration\Mysql;

use App\Domain\Usuario\ActualizarUsuarioDTO;
use App\Domain\Usuario\NuevoUsuarioDTO;
use App\Infrastructure\Persistence\Mysql\UsuarioRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

final class UsuarioRepositoryTest extends TestCase
{
    private Connection $connection;
    private UsuarioRepository $repository;
    private int $idUsuarioCreado;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection([
            'driverClass' => \Doctrine\DBAL\Driver\Mysqli\Driver::class,
            'host' => getenv('DB_HOST'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'dbname' => getenv('DB_NAME'),
            'charset' => 'utf8mb4',
        ]);

        $this->repository = new UsuarioRepository($this->connection);

        $this->repository->insertar(new NuevoUsuarioDTO(
            nickName: 'test_' . bin2hex(random_bytes(6)),
            telefono: '600000000',
            correo: 'test_' . bin2hex(random_bytes(6)) . '@example.com',
            password: 'secret123',
            nombre: 'Test',
            apellidos: 'User',
            edad: 30,
            rol: 'registrado',
        ));

        $this->idUsuarioCreado = (int) $this->connection->lastInsertId();
    }

    protected function tearDown(): void
    {
        $this->connection->delete('usuario', ['idUsuario' => $this->idUsuarioCreado]);
    }

    public function testPorIdDevuelveElUsuarioCreado(): void
    {
        $usuario = $this->repository->porId($this->idUsuarioCreado);

        self::assertNotNull($usuario);
        self::assertSame($this->idUsuarioCreado, $usuario->idUsuario);
        self::assertSame('Test', $usuario->nombre);
    }

    public function testPorIdNoExistenteDevuelveNull(): void
    {
        self::assertNull($this->repository->porId(999999999));
    }

    public function testCheckLoginConCredencialesCorrectas(): void
    {
        $usuario = $this->repository->porId($this->idUsuarioCreado);

        self::assertTrue($this->repository->checkLogin($usuario->nickName, 'secret123'));
        self::assertFalse($this->repository->checkLogin($usuario->nickName, 'wrong-password'));
    }

    public function testEsPremiumYActivarPremiumDemo(): void
    {
        self::assertFalse($this->repository->esPremium($this->idUsuarioCreado));

        $this->repository->activarPremiumDemo($this->idUsuarioCreado);

        self::assertTrue($this->repository->esPremium($this->idUsuarioCreado));
    }

    public function testTokenRecuperacionSeAlmacenaYSeRecupera(): void
    {
        $usuario = $this->repository->porId($this->idUsuarioCreado);
        $token = $this->repository->generarTokenRecuperacion();

        $this->repository->insertarTokenRecuperacion($usuario->nickName, $token);

        self::assertSame($token, $this->repository->tokenRecuperacion($usuario->correo));
    }

    public function testModificarActualizaLosDatos(): void
    {
        $usuario = $this->repository->porId($this->idUsuarioCreado);

        $this->repository->modificar(new ActualizarUsuarioDTO(
            idUsuario: $this->idUsuarioCreado,
            nickName: $usuario->nickName,
            telefono: $usuario->telefono,
            correo: $usuario->correo,
            password: 'nuevoPassword123',
            nombre: 'Modificado',
            apellidos: $usuario->apellidos,
            edad: $usuario->edad,
            rol: $usuario->rol,
        ));

        $actualizado = $this->repository->porId($this->idUsuarioCreado);

        self::assertSame('Modificado', $actualizado->nombre);
        self::assertTrue($this->repository->checkLogin($usuario->nickName, 'nuevoPassword123'));
    }
}
