<?php

require 'usuario/usuario.php';
require 'actividad/actividad.php';

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

    public function getUsuarioPorId($idUsuario)
    {
        //echo $idUsuario;
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

        //echo $id;

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

        if (true/*password_verify($password, $usuario->getPassword())*/) {
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
    public function insertarActividadSimple($nombreActividad, $descripcion, $tipoActividad, $duracion)
    {
        // Primero, insertar los datos comunes en la tabla Actividad
        $queryActividad = "INSERT INTO Actividad (nombreActividad, descripcion, tipoActividad, duracion) VALUES (?, ?, ?, ?)";
        $stmtActividad = $this->mysqli->prepare($queryActividad);
        $stmtActividad->bind_param('sssi', $nombreActividad, $descripcion, $tipoActividad, $duracion);

        try {
            $stmtActividad->execute();
            $idActividad = $stmtActividad->insert_id; // Obtener el ID de la actividad insertada
            echo $idActividad;
        } catch (PDOException $e) {
            echo "Error al insertar actividad: " . $e->getMessage();
            return false; // Fallo al insertar la actividad
        }

        // Ahora, insertar los datos específicos de ActividadSimple en su tabla correspondiente
        $queryActividadSimple = "INSERT INTO ActividadSimple (idActividad) VALUES (?)";
        $stmtActividadSimple = $this->mysqli->prepare($queryActividadSimple);
        $stmtActividadSimple->bind_param('i', $idActividad);

        try {
            $stmtActividadSimple->execute();
            return $idActividad; // Éxito al insertar la actividad simple
        } catch (PDOException $e) {
            echo "Error al insertar actividad simple: " . $e->getMessage();
            return false; // Fallo al insertar la actividad simple
        }
    }
    // Función para modificar una actividad existente
    public function modificarActividad($idActividad, $nombreActividad, $descripcion, $tipoActividad, $duracion)
    {
        $query = "UPDATE Actividad SET nombreActividad = ?, descripcion = ?, tipoActividad = ?, duracion = ? WHERE idActividad = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('sssii', $nombreActividad, $descripcion, $tipoActividad, $duracion, $idActividad);

        try {
            $stmt->execute();
            return true; // Éxito al modificar la actividad
        } catch (PDOException $e) {
            echo "Error al modificar la actividad: " . $e->getMessage();
            return false; // Fallo al modificar la actividad
        }
    }

    public function eliminarActividad($idActividad)
    {
        // Eliminar todas las fotos de la galería asociadas a esta actividad
        if (!$this->eliminarFotosGaleria($idActividad)) {
            // Si hubo un error al eliminar las fotos, retornar false
            return false;
        }

        // Eliminar la actividad de la tabla de actividades
        $query = "DELETE FROM Actividad WHERE idActividad = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $idActividad);

        try {
            $stmt->execute();
            return true; // Éxito al eliminar la actividad
        } catch (PDOException $e) {
            echo "Error al eliminar la actividad: " . $e->getMessage();
            return false; // Fallo al eliminar la actividad
        }
    }

    public function eliminarFotoGaleria($numImagen)
    {
        // Eliminar todas las fotos de la galería asociadas a la actividad
        $query = "DELETE FROM GaleriaFotos WHERE numImagen = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $numImagen);

        try {
            $stmt->execute();
            return true; // Éxito al eliminar las fotos de la galería
        } catch (PDOException $e) {
            echo "Error al elimina la foto: " . $e->getMessage();
            return false; // Fallo al eliminar las fotos de la galería
        }
    }

    public function agregarFotoGaleria($idActividad, $foto)
    {
        // Insertar una foto en la galería asociada a la actividad
        $query = "INSERT INTO GaleriaFotos (idActividad, url) VALUES (?, ?)";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('is', $idActividad, $foto);

        try {
            $stmt->execute();
            return true; // Éxito al agregar la foto a la galería
        } catch (PDOException $e) {
            echo "Error al agregar foto a la galería: " . $e->getMessage();
            return false; // Fallo al agregar la foto a la galería
        }
    }

    private function eliminarFotosGaleria($idActividad)
    {
        // Eliminar todas las fotos de la galería asociadas a la actividad
        $query = "DELETE FROM GaleriaFotos WHERE idActividad = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $idActividad);

        try {
            $stmt->execute();
            return true; // Éxito al eliminar las fotos de la galería
        } catch (PDOException $e) {
            echo "Error al eliminar las fotos de la galería: " . $e->getMessage();
            return false; // Fallo al eliminar las fotos de la galería
        }
    }

    public function buscarCoincidenciasActividad($nombreActividad)
    {
        $sql = "SELECT * FROM actividad WHERE nombreActividad LIKE CONCAT('%', ? , '%')";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $nombreActividad);

        $stmt->execute();

        $result = $stmt->get_result();

        $actividades = array();

        while ($row = $result->fetch_assoc()) {
            $actividad = array(
                'idActividad' => $row['idActividad'],
                'nombreActividad' => $row['nombreActividad'],
                'descripcion' => $row['descripcion'],
                'tipoActividad' => $row['tipoActividad'],
                'duracion' => $row['duracion'],
            );

            $actividades[] = $actividad;
        }

        return $actividades;
    }

    public function getActividad($idActividad)
    {
        $query = "SELECT nombreActividad, descripcion, tipoActividad, duracion FROM Actividad WHERE idActividad = ?";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $idActividad);
        $stmt->execute();

        // Obtener el resultado como un objeto de la clase ActividadSimple
        $resultado = $stmt->get_result();

        // Verificar si se encontró la actividad simple
        if ($resultado->num_rows > 0) {
            // Obtener el primer resultado como un objeto de la clase ActividadSimple
            $fila = $resultado->fetch_assoc();

            // Crear una instancia de la clase ActividadSimple con los datos obtenidos de la base de datos
            $actividadSimpleEncontrada = new ActividadSimple(
                $fila['nombreActividad'],
                $fila['descripcion'],
                $fila['tipoActividad'],
                $fila['duracion'],
            );

            // Obtener la galería de fotos asociada a la actividad simple
            $queryGaleria = "SELECT url FROM GaleriaFotos WHERE idActividad = ?";
            $stmtGaleria = $this->mysqli->prepare($queryGaleria);
            $stmtGaleria->bind_param('i', $idActividad);
            $stmtGaleria->execute();
            $resultadoGaleria = $stmtGaleria->get_result();

            // Verificar si se encontraron fotos en la galería
            if ($resultadoGaleria->num_rows > 0) {
                // Iterar sobre los resultados y agregar las URLs al array de galería de fotos de la actividad simple
                while ($filaGaleria = $resultadoGaleria->fetch_assoc()) {
                    $actividadSimpleEncontrada->aniadirFotosGaleria($filaGaleria['url']);
                }
            }

            //echo var_dump($actividadSimpleEncontrada->getGaleriaFotos());

            return $actividadSimpleEncontrada;
        } else {
            return null; // No se encontró una actividad simple con el ID dado
        }
    }

    public function getGaleriaActividad($idActividad)
    {
        $sql = "SELECT * FROM galeriafotos WHERE idActividad = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idActividad);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $galeria = array();

        // Verificar si se encontraron resultados
        if ($resultado->num_rows > 0) {
            // Iterar sobre los resultados y agregar las imágenes al array de galería
            while ($row = $resultado->fetch_assoc()) {
                $imagen = new Imagen(
                    $row['numImagen'],
                    $row['idActividad'],
                    $row['url']
                );
                $galeria[] = $imagen;
            }
        }

        return $galeria;
    }

}
