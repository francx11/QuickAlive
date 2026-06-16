<?php

declare(strict_types=1);

namespace Tests\Integration\Mysql;

use App\Domain\Actividad\NuevaActividadGeolocalizableDTO;
use App\Domain\Actividad\NuevaActividadSimpleDTO;
use App\Infrastructure\Persistence\Mysql\ActividadRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

final class ActividadRepositoryTest extends TestCase
{
    private Connection $connection;
    private ActividadRepository $repository;
    private int $idUsuario;
    private int $idTipoPreferencia;

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

        $this->repository = new ActividadRepository($this->connection);

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

        $this->connection->insert('tipopreferencias', ['tipoPreferencia' => 'test_' . bin2hex(random_bytes(6))]);
        $this->idTipoPreferencia = (int) $this->connection->lastInsertId();
    }

    protected function tearDown(): void
    {
        $this->connection->delete('usuario', ['idUsuario' => $this->idUsuario]);
        $this->connection->delete('tipopreferencias', ['idTipoPreferencia' => $this->idTipoPreferencia]);
    }

    public function testInsertarSimpleYPorId(): void
    {
        $idActividad = $this->repository->insertarSimple(new NuevaActividadSimpleDTO('Senderismo', 'Ruta de montaña', 90));

        $actividad = $this->repository->porId($idActividad);

        self::assertNotNull($actividad);
        self::assertSame('Senderismo', $actividad->nombreActividad);
        self::assertSame([], $actividad->galeriaFotos);

        $this->repository->eliminar($idActividad);
    }

    public function testInsertarGeolocalizableYGeolocalizablePorId(): void
    {
        $idActividad = $this->repository->insertarGeolocalizable(new NuevaActividadGeolocalizableDTO(
            nombreActividad: 'Concierto',
            descripcion: 'Concierto en directo',
            duracion: 120,
            urlImagen: 'https://example.com/img.png',
            idApi: 'ext-123',
            fechaLimite: '2026-12-31',
        ));

        $actividad = $this->repository->geolocalizablePorId($idActividad);

        self::assertNotNull($actividad);
        self::assertSame('ext-123', $actividad->idApi);

        $this->repository->eliminar($idActividad);
    }

    public function testAgregarYEliminarFotoGaleria(): void
    {
        $idActividad = $this->repository->insertarSimple(new NuevaActividadSimpleDTO('Yoga', 'Sesión de yoga', 60));

        $idFoto = $this->repository->agregarFotoGaleria($idActividad, 'https://example.com/foto.png');
        self::assertGreaterThan(0, $idFoto);
        self::assertCount(1, $this->repository->galeria($idActividad));

        $this->repository->eliminarFotoGaleria($idFoto);
        self::assertCount(0, $this->repository->galeria($idActividad));

        $this->repository->eliminar($idActividad);
    }

    public function testMarcarRealizadaPendientesYCompletar(): void
    {
        $idActividad = $this->repository->insertarSimple(new NuevaActividadSimpleDTO('Natación', 'Piscina', 45));

        $this->repository->marcarRealizada($this->idUsuario, $idActividad);
        self::assertCount(1, $this->repository->pendientesPorUsuario($this->idUsuario));

        $this->repository->completar($this->idUsuario, $idActividad);
        self::assertCount(0, $this->repository->pendientesPorUsuario($this->idUsuario));
        self::assertCount(1, $this->repository->historialPorUsuario($this->idUsuario));

        $this->repository->eliminar($idActividad);
    }

    public function testRechazarYNoEstaEnRechazadas(): void
    {
        $idActividad = $this->repository->insertarSimple(new NuevaActividadSimpleDTO('Escalada', 'Rocódromo', 90));

        self::assertTrue($this->repository->noEstaEnRechazadas($idActividad));

        $this->repository->rechazar($this->idUsuario, $idActividad);

        self::assertFalse($this->repository->noEstaEnRechazadas($idActividad));

        $this->repository->eliminar($idActividad);
    }

    public function testCategoriasYTodasConCategorias(): void
    {
        $idActividad = $this->repository->insertarSimple(new NuevaActividadSimpleDTO('Ciclismo', 'Ruta en bici', 60));

        $this->repository->insertarCategorias($idActividad, [$this->idTipoPreferencia]);

        $categorias = $this->repository->categorias($idActividad);
        self::assertCount(1, $categorias);
        self::assertSame($this->idTipoPreferencia, $categorias[0]['idTipoPreferencia']);

        $this->repository->eliminar($idActividad);
    }
}
