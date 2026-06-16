<?php

declare(strict_types=1);

namespace Tests\Integration\Mysql;

use App\Infrastructure\Persistence\Mysql\PreferenciaRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

final class PreferenciaRepositoryTest extends TestCase
{
    private Connection $connection;
    private PreferenciaRepository $repository;
    private int $idUsuario;

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

        $this->repository = new PreferenciaRepository($this->connection);

        $this->connection->insert('usuario', [
            'nickName' => 'test_' . bin2hex(random_bytes(6)),
            'telefono' => '600000000',
            'correo' => 'test_' . bin2hex(random_bytes(6)) . '@example.com',
            'password' => 'hash',
            'nombre' => 'Test',
            'apellidos' => 'User',
            'edad' => 30,
            'rol' => 'registrado',
        ]);
        $this->idUsuario = (int) $this->connection->lastInsertId();
    }

    protected function tearDown(): void
    {
        $this->connection->delete('usuario', ['idUsuario' => $this->idUsuario]);
    }

    public function testInsertarTipoNombreYEliminar(): void
    {
        $idTipo = $this->repository->insertarTipo('Deportes_' . bin2hex(random_bytes(4)));

        self::assertGreaterThan(0, $idTipo);
        self::assertNotNull($this->repository->nombreTipo($idTipo));

        $this->repository->eliminarTipo($idTipo);

        self::assertNull($this->repository->nombreTipo($idTipo));
    }

    public function testPreferenciasUsuarioCicloCompleto(): void
    {
        $idTipo = $this->repository->insertarTipo('Musica_' . bin2hex(random_bytes(4)));

        self::assertNull($this->repository->preferenciasUsuario($this->idUsuario));

        $this->repository->insertarPreferenciaPersonal($this->idUsuario, 'Musica', $idTipo);

        $preferencias = $this->repository->preferenciasUsuario($this->idUsuario);
        self::assertNotNull($preferencias);
        self::assertCount(1, $preferencias);
        self::assertSame($idTipo, $preferencias[0]->idTipoPreferencia);

        $this->repository->eliminarPreferenciaPersonal($this->idUsuario, 'Musica', $idTipo);
        self::assertNull($this->repository->preferenciasUsuario($this->idUsuario));

        $this->repository->eliminarTipo($idTipo);
    }

    public function testActualizarPreferenciasPersonalesReemplazaLasExistentes(): void
    {
        $idTipo1 = $this->repository->insertarTipo('Lectura_' . bin2hex(random_bytes(4)));
        $idTipo2 = $this->repository->insertarTipo('Cine_' . bin2hex(random_bytes(4)));

        $this->repository->insertarPreferenciaPersonal($this->idUsuario, 'Lectura', $idTipo1);

        $this->repository->actualizarPreferenciasPersonales($this->idUsuario, [
            ['nombreTipoPreferencia' => 'Cine', 'idTipoPreferencia' => $idTipo2],
        ]);

        $preferencias = $this->repository->preferenciasUsuario($this->idUsuario);
        self::assertCount(1, $preferencias);
        self::assertSame($idTipo2, $preferencias[0]->idTipoPreferencia);

        $this->repository->eliminarTipo($idTipo1);
        $this->repository->eliminarTipo($idTipo2);
    }

    public function testIdsTipoPreferenciaFaltantes(): void
    {
        $idTipo = $this->repository->insertarTipo('Viajes_' . bin2hex(random_bytes(4)));

        $faltantes = $this->repository->idsTipoPreferenciaFaltantes($this->idUsuario);

        self::assertContains($idTipo, $faltantes);

        $this->repository->eliminarTipo($idTipo);
    }
}
