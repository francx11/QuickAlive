<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mysql;

use App\Domain\Usuario\ActualizarUsuarioDTO;
use App\Domain\Usuario\NuevoUsuarioDTO;
use App\Domain\Usuario\Usuario;
use App\Domain\Usuario\UsuarioRepositoryInterface;
use Doctrine\DBAL\Connection;

final readonly class UsuarioRepository implements UsuarioRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function porId(int $idUsuario): ?Usuario
    {
        $row = $this->connection->fetchAssociative(
            'SELECT idUsuario, nickName, telefono, correo, password, nombre, apellidos, edad, rol, isPremium FROM usuario WHERE idUsuario = ?',
            [$idUsuario],
        );

        return $row === false ? null : Usuario::fromRow($row);
    }

    public function porCorreo(string $correo): ?Usuario
    {
        $row = $this->connection->fetchAssociative(
            'SELECT idUsuario, nickName, telefono, correo, password, nombre, apellidos, edad, rol, isPremium FROM usuario WHERE correo = ?',
            [$correo],
        );

        return $row === false ? null : Usuario::fromRow($row);
    }

    public function porNickName(string $nickName): ?Usuario
    {
        $row = $this->connection->fetchAssociative(
            'SELECT idUsuario, nickName, telefono, correo, password, nombre, apellidos, edad, rol, isPremium FROM usuario WHERE nickName = ?',
            [$nickName],
        );

        return $row === false ? null : Usuario::fromRow($row);
    }

    public function insertar(NuevoUsuarioDTO $datos): bool
    {
        $affected = $this->connection->insert('usuario', [
            'nickName' => $datos->nickName,
            'telefono' => $datos->telefono,
            'correo' => $datos->correo,
            'password' => password_hash($datos->password, PASSWORD_DEFAULT),
            'nombre' => $datos->nombre,
            'apellidos' => $datos->apellidos,
            'edad' => $datos->edad,
            'rol' => $datos->rol,
        ]);

        return $affected > 0;
    }

    public function modificar(ActualizarUsuarioDTO $datos): bool
    {
        $affected = $this->connection->update(
            'usuario',
            [
                'nickName' => $datos->nickName,
                'telefono' => $datos->telefono,
                'correo' => $datos->correo,
                'password' => password_hash($datos->password, PASSWORD_DEFAULT),
                'nombre' => $datos->nombre,
                'apellidos' => $datos->apellidos,
                'edad' => $datos->edad,
                'rol' => $datos->rol,
            ],
            ['idUsuario' => $datos->idUsuario],
        );

        return $affected > 0;
    }

    public function modificarContrasena(string $correo, string $nuevaContrasena): bool
    {
        $affected = $this->connection->update(
            'usuario',
            ['password' => password_hash($nuevaContrasena, PASSWORD_DEFAULT)],
            ['correo' => $correo],
        );

        return $affected > 0;
    }

    public function eliminar(int $idUsuario): bool
    {
        return $this->connection->delete('usuario', ['idUsuario' => $idUsuario]) > 0;
    }

    public function checkLogin(string $nickName, string $password): bool
    {
        $usuario = $this->porNickName($nickName);

        return $usuario !== null && password_verify($password, $usuario->password);
    }

    public function buscarCoincidencias(string $nickName): array
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT idUsuario, nickName, correo, nombre, apellidos FROM usuario WHERE nickName LIKE CONCAT('%', ?, '%')",
            [$nickName],
        );

        return array_map(
            static fn (array $row): array => [
                'idUsuario' => (int) $row['idUsuario'],
                'nickName' => $row['nickName'],
                'correo' => $row['correo'],
                'nombre' => $row['nombre'],
                'apellidos' => $row['apellidos'],
            ],
            $rows,
        );
    }

    public function esPremium(int $idUsuario): bool
    {
        $isPremium = $this->connection->fetchOne('SELECT isPremium FROM usuario WHERE idUsuario = ?', [$idUsuario]);

        return $isPremium !== false && (bool) $isPremium;
    }

    public function activarPremiumDemo(int $idUsuario): bool
    {
        return $this->connection->update('usuario', ['isPremium' => 1], ['idUsuario' => $idUsuario]) > 0;
    }

    public function tokenRecuperacion(string $correo): ?string
    {
        $token = $this->connection->fetchOne('SELECT tokenRecuperacion FROM usuario WHERE correo = ?', [$correo]);

        return $token === false ? null : $token;
    }

    public function insertarTokenRecuperacion(string $nickName, string $tokenRecuperacion): bool
    {
        $affected = $this->connection->update(
            'usuario',
            ['tokenRecuperacion' => $tokenRecuperacion],
            ['nickName' => $nickName],
        );

        return $affected > 0;
    }

    public function generarTokenRecuperacion(): string
    {
        return bin2hex(random_bytes(32));
    }
}
