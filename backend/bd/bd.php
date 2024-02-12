<?php

require 'usuario/usuario.php';

class BD
{
    public $mysqli;

    public function iniciarConexion()
    {
        return $this->mysqli;
    }

    public function cerrarConexion()
    {
        mysqli_close($this->mysqli);
    }

    public function __construct()
    {
        $this->mysqli = new mysqli("127.0.0.1", "root", "", "quickalivedb");

        if ($this->mysqli->connect_errno) {

            echo ("Fallo en la conexion: " . $this->mysqli->connect_errno);

        }

        return $this->mysqli;
    }

    // Funciones de administración del usuario

    public function getUsuarioId($idUsuario)
    {
        echo $idUsuario;
        $query = "SELECT nickName, telefono, correo, password, nombre, apellidos, edad, rol FROM usuario WHERE idUsuario = ?";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();

        // Obtener el resultado como un objeto de la clase Usuario
        $resultado = $stmt->get_result();

        // Verificar si se encontró un usuario
        if ($resultado->num_rows > 0) {
            // Obtener el primer resultado como un objeto de la clase Usuario
            $fila = $resultado->fetch_assoc();

            // Crear una instancia de la clase Usuario con los datos obtenidos de la base de datos
            $usuarioEncontrado = new Usuario(
                $fila['nickName'],
                $fila['telefono'],
                $fila['correo'],
                $fila['password'],
                $fila['nombre'],
                $fila['apellidos'],
                $fila['edad'],
                $fila['rol']
            );

            return $usuarioEncontrado;
        } else {
            return null; // No se encontró un usuario con el Id dado
        }
    }

    public function getUsuario($nickName)
    {
        $query = "SELECT nickName,telefono,correo,password,nombre,apellidos,edad,rol FROM usuario WHERE nickName = ?";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $nickName);
        $stmt->execute();

        // Obtener el resultado como un objeto de la clase Usuario
        $resultado = $stmt->get_result();

        // Verificar si se encontró un usuario
        if ($resultado->num_rows > 0) {
            // Obtener el primer resultado como un objeto de la clase Usuario
            $fila = $resultado->fetch_assoc();

            // Crear una instancia de la clase Usuario con los datos obtenidos de la base de datos
            $usuarioEncontrado = new Usuario(
                $fila['nickName'],
                $fila['telefono'],
                $fila['correo'],
                $fila['password'],
                $fila['nombre'],
                $fila['apellidos'],
                $fila['edad'],
                $fila['rol']
            );

            return $usuarioEncontrado;
        } else {
            return null; // No se encontró un usuario con el nickName dado
        }

    }

    public function insertarUsuario($nickName, $telefono, $correo, $password, $nombre, $apellidos, $edad, $rol)
    {
        // Hash de la contraseña
        $password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO usuario (nickName, telefono, correo, password, nombre, apellidos, edad, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ssssssis', $nickName, $telefono, $correo, $password, $nombre, $apellidos, $edad, $rol);

        try {
            $stmt->execute();
            return true; // Éxito al insertar el usuario
        } catch (PDOException $e) {
            echo "Error al insertar usuario: " . $e->getMessage();
            return false; // Fallo al insertar el usuario
        }
    }

    public function modificarUsuario($id, $nickName, $telefono, $correo, $password, $nombre, $apellidos, $edad, $rol)
    {
        // Hash de la contraseña
        $password = password_hash($password, PASSWORD_DEFAULT);

        $query = "UPDATE usuario SET nickName = ?, telefono = ?, correo = ?, password = ?, nombre = ?, apellidos = ?, edad = ?, rol = ? WHERE idUsuario = ?";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ssssssisi', $nickName, $telefono, $correo, $password, $nombre, $apellidos, $edad, $rol, $id);

        echo $id;

        try {
            $stmt->execute();
            return true; // Éxito al modificar el usuario
        } catch (PDOException $e) {
            echo "Error al modificar usuario: " . $e->getMessage();
            return false; // Fallo al modificar el usuario
        }
    }

    public function eliminarUsuario($id)
    {
        $query = "DELETE FROM usuario WHERE idUsuario = ?";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $id);

        try {
            $stmt->execute();
            return true; // Éxito al eliminar el usuario
        } catch (PDOException $e) {
            echo "Error al eliminar usuario: " . $e->getMessage();
            return false; // Fallo al eliminar el usuario
        }
    }

    public function getRol($nickName)
    {
        $usuario = $this->getUsuario($nickName);
        $rol = $usuario->getRol();

        return $rol;
    }

    public function checkLogin($nickName, $password)
    {

        $usuario = $this->getUsuario($nickName);

        if ($usuario == null) {
            echo 'No existe el usuario';
            return false;
        }

        if (password_verify($password, $usuario->getPassword())) {
            echo 'Contraseña correcta';
            return true;
        }

        return false;
    }

    public function buscarCoincidenciasUsuario($nickName)
    {
        $sql = "SELECT * FROM usuario WHERE nickName LIKE CONCAT('%', ? , '%')";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $nickName);

        $stmt->execute();

        $result = $stmt->get_result();

        $usuarios = array();

        while ($row = $result->fetch_assoc()) {
            $usuario = array(
                'idUsuario' => $row['idUsuario'],
                'nickName' => $row['nickName'],
                'correo' => $row['correo'],
                'nombre' => $row['nombre'],
                'apellidos' => $row['apellidos'],
            );

            $usuarios[] = $usuario;
        }

        return $usuarios;
    }

    // Gestión de las actividades

}
