<?php

require 'usuario/usuario.php';
require 'actividad/actividad.php';

/**
 * Clase que representa la conexión y operaciones con la base de datos.
 */
class BD
{
    public $mysqli;

    /**
     * Inicia la conexión con la base de datos.
     *
     * @return object Objeto de conexión a la base de datos.
     */
    public function iniciarConexion()
    {
        return $this->mysqli;
    }

    /**
     * Cierra la conexión con la base de datos.
     */
    public function cerrarConexion()
    {
        mysqli_close($this->mysqli);
    }

    /**
     * Constructor de la clase BD. Establece la conexión con la base de datos.
     *
     * @return object Objeto de conexión a la base de datos.
     */
    public function __construct()
    {
        $this->mysqli = new mysqli(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASS"), getenv("DB_NAME"));

        if ($this->mysqli->connect_errno) {
            echo ("Fallo en la conexion: " . $this->mysqli->connect_errno);
        }

        $this->iniciarConexion();

        return $this->mysqli;
    }

    /**
     * Destructor de la clase BD. Cierra la conexión con la base de datos utilizando el método cerrarConexion().
     */
    public function __destruct()
    {
        $this->cerrarConexion();
    }

    /**
     * Verifica si el token de recuperación proporcionado por el usuario coincide con el almacenado en la base de datos.
     *
     * @param string $tokenRecuperacionUsuario Token de recuperación proporcionado por el usuario.
     * @param string $tokenRecuperacionBD Token de recuperación almacenado en la base de datos.
     * @return bool true si los tokens coinciden, false si no coinciden.
     */
    public function verificarTokenRecuperacion($tokenRecuperacionUsuario, $tokenRecuperacionBD)
    {
        if ($tokenRecuperacionUsuario === $tokenRecuperacionBD) {
            return true; // Los tokens coinciden
        } else {
            return false; // Los tokens no coinciden
        }
    }

    /**
     * Genera un token de recuperación aleatorio.
     *
     * @return string Token de recuperación generado.
     */
    public function generarTokenRecuperacion()
    {
        $longitud = 32; // Longitud del token

        // Genera una cadena de bytes aleatorios
        $bytesAleatorios = random_bytes($longitud);

        // Convierte los bytes a una cadena hexadecimal
        $tokenRecuperacion = bin2hex($bytesAleatorios);

        return $tokenRecuperacion;
    }

    /**
     * Obtiene el token de recuperación de contraseña asociado a un correo electrónico.
     *
     * @param string $correo Correo electrónico del usuario.
     * @return string|null Token de recuperación de contraseña o null si no se encuentra.
     */
    public function getTokenRecuperacion($correo)
    {
        $query = "SELECT tokenRecuperacion FROM usuario WHERE correo = ?";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $correo);
        $stmt->execute();

        // Obtiene el resultado como un array asociativo
        $resultado = $stmt->get_result();

        // Verifica si se encontró un usuario
        if ($resultado->num_rows > 0) {
            // Obtiene el primer resultado como un array asociativo
            $fila = $resultado->fetch_assoc();

            // Obtiene el token de recuperación de contraseña
            $tokenRecuperacion = $fila['tokenRecuperacion'];

            return $tokenRecuperacion;
        } else {
            return null; // No se encontró un usuario con el correo electrónico dado
        }
    }

    /**
     * Inserta un token de recuperación en la base de datos para un usuario específico.
     *
     * @param string $nickName Nombre de usuario del usuario.
     * @param string $tokenRecuperacion Token de recuperación a insertar.
     * @return bool true si la inserción fue exitosa, false si falló.
     */
    public function insertarTokenRecuperacion($nickName, $tokenRecuperacion)
    {
        // Consulta SQL para insertar el token de recuperación
        $query = "UPDATE usuario SET tokenRecuperacion = ? WHERE nickName = ?";

        // Preparar la consulta
        $stmt = $this->mysqli->prepare($query);

        // Verificar si la preparación de la consulta fue exitosa
        if ($stmt === false) {
            echo "Error al preparar la consulta para insertar token de recuperación.";
            return false;
        }

        // Vincular los parámetros y ejecutar la consulta
        $stmt->bind_param("ss", $tokenRecuperacion, $nickName);
        $result = $stmt->execute();

        // Verificar si la ejecución de la consulta fue exitosa
        if ($result === false) {
            echo "Error al insertar el token de recuperación en la base de datos.";
            return false;
        }

        return true; // Éxito al insertar el token de recuperación
    }

    /**
     * Obtiene un usuario de la base de datos por su ID.
     *
     * @param int $idUsuario ID del usuario a buscar.
     * @return Usuario|null Objeto Usuario si se encontró, o null si no se encontró.
     */
    public function getUsuarioPorId($idUsuario)
    {
        $query = "SELECT idUsuario, nickName, telefono, correo, password, nombre, apellidos, edad, rol FROM usuario WHERE idUsuario = ?";

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
                $fila['idUsuario'],
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

    /**
     * Obtiene un usuario de la base de datos por su correo electrónico.
     *
     * @param string $correo Correo electrónico del usuario a buscar.
     * @return Usuario|null Objeto Usuario si se encontró, o null si no se encontró.
     */
    public function getUsuarioPorCorreo($correo)
    {
        $query = "SELECT idUsuario, nickName, telefono, correo, password, nombre, apellidos, edad, rol FROM usuario WHERE correo = ?";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $correo);
        $stmt->execute();

        // Obtener el resultado como un objeto de la clase Usuario
        $resultado = $stmt->get_result();

        // Verificar si se encontró un usuario
        if ($resultado->num_rows > 0) {
            // Obtener el primer resultado como un objeto de la clase Usuario
            $fila = $resultado->fetch_assoc();

            // Crear una instancia de la clase Usuario con los datos obtenidos de la base de datos
            $usuarioEncontrado = new Usuario(
                $fila['idUsuario'],
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
            return null; // No se encontró un usuario con el correo dado
        }
    }

    /**
     * Obtiene un usuario de la base de datos por su nombre de usuario (nickName).
     *
     * @param string $nickName Nombre de usuario (nickName) del usuario a buscar.
     * @return Usuario|null Objeto Usuario si se encontró, o null si no se encontró.
     */
    public function getUsuario($nickName)
    {
        $query = "SELECT idUsuario, nickName, telefono, correo, password, nombre, apellidos, edad, rol FROM usuario WHERE nickName = ?";

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
                $fila['idUsuario'],
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

    /**
     * Inserta un nuevo usuario en la base de datos.
     *
     * @param string $nickName Nombre de usuario del nuevo usuario.
     * @param string $telefono Número de teléfono del nuevo usuario.
     * @param string $correo Correo electrónico del nuevo usuario.
     * @param string $password Contraseña del nuevo usuario (ya debe estar hasheada).
     * @param string $nombre Nombre del nuevo usuario.
     * @param string $apellidos Apellidos del nuevo usuario.
     * @param int $edad Edad del nuevo usuario.
     * @param string $rol Rol del nuevo usuario.
     * @return bool true si la inserción fue exitosa, false si falló.
     */
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

    /**
     * Modifica los datos de un usuario existente en la base de datos.
     *
     * @param int $id ID del usuario a modificar.
     * @param string $nickName Nuevo nombre de usuario.
     * @param string $telefono Nuevo número de teléfono.
     * @param string $correo Nuevo correo electrónico.
     * @param string $password Nueva contraseña (ya debe estar hasheada).
     * @param string $nombre Nuevo nombre.
     * @param string $apellidos Nuevos apellidos.
     * @param int $edad Nueva edad.
     * @param string $rol Nuevo rol.
     * @return bool true si la modificación fue exitosa, false si falló.
     */
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

    /**
     * Modifica la contraseña de un usuario en la base de datos.
     *
     * @param string $correo Correo electrónico del usuario.
     * @param string $nuevaContraseña Nueva contraseña (ya debe estar hasheada).
     * @return bool true si la modificación fue exitosa, false si falló.
     */
    public function modificarContraseñaUsuario($correo, $nuevaContraseña)
    {
        // Hash de la nueva contraseña
        $passwordHash = password_hash($nuevaContraseña, PASSWORD_DEFAULT);

        $query = "UPDATE usuario SET password = ? WHERE correo = ?";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ss', $passwordHash, $correo);

        try {
            $stmt->execute();
            return true; // Éxito al modificar la contraseña del usuario
        } catch (PDOException $e) {
            echo "Error al modificar la contraseña del usuario: " . $e->getMessage();
            return false; // Fallo al modificar la contraseña del usuario
        }
    }

    /**
     * Elimina un usuario de la base de datos.
     *
     * @param int $id ID del usuario a eliminar.
     * @return bool true si la eliminación fue exitosa, false si falló.
     */
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

    /**
     * Obtiene el rol de un usuario por su nombre de usuario (nickName).
     *
     * @param string $nickName Nombre de usuario del usuario.
     * @return string Rol del usuario.
     */
    public function getRol($nickName)
    {
        $usuario = $this->getUsuario($nickName);
        $rol = $usuario->getRol();

        return $rol;
    }
    /**
     * Obtiene el id de un usuario por su nombre de usuario (nickName).
     *
     * @param string $nickName Nombre de usuario del usuario.
     * @return string Id del usuario.
     */
    public function getIdUsuario($nickName)
    {
        $usuario = $this->getUsuario($nickName);
        $rol = $usuario->getIdUsuario();

        return $rol;
    }

    /**
     * Verifica las credenciales de inicio de sesión de un usuario.
     *
     * @param string $nickName Nombre de usuario del usuario.
     * @param string $password Contraseña proporcionada por el usuario.
     * @return bool true si las credenciales son válidas, false si no lo son.
     */
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

    /**
     * Busca usuarios cuyo nombre de usuario (nickName) coincide parcialmente con el nombre proporcionado.
     *
     * @param string $nickName Parte del nombre de usuario a buscar.
     * @return array Arreglo de usuarios que coinciden con el nombre de usuario proporcionado.
     */
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

    /**
     * Inserta una nueva actividad simple en la base de datos.
     *
     * @param string $nombreActividad Nombre de la actividad.
     * @param string $descripcion Descripción de la actividad.
     * @param int $duracion Duración de la actividad.
     * @return int|bool ID de la actividad insertada si la inserción fue exitosa, false si falló.
     */
    public function insertarActividadSimple($nombreActividad, $descripcion, $duracion)
    {
        // Primero, insertar los datos comunes en la tabla Actividad
        $queryActividad = "INSERT INTO Actividad (nombreActividad, descripcion, duracion) VALUES (?, ?, ?)";
        $stmtActividad = $this->mysqli->prepare($queryActividad);
        $stmtActividad->bind_param('ssi', $nombreActividad, $descripcion, $duracion);

        try {
            $stmtActividad->execute();
            $idActividad = $stmtActividad->insert_id; // Obtener el ID de la actividad insertada
            //echo $idActividad;
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

    /**
     * Inserta las categorías de una actividad.
     *
     * @param string $idActividad Nombre de la actividad.
     * @param array $categorias Array de categorías con sus IDs de tipo de preferencia.
     * @return bool true si la inserción fue exitosa, false si falló.
     */
    public function insertarActividadConCategorias($idActividad, $categorias)
    {
        // Insertar las categorías de la actividad en la tabla Actividad_TipoPreferencia
        $queryInsertCategoria = "INSERT INTO Actividad_TipoPreferencia (idActividad, idTipoPreferencia) VALUES (?, ?)";
        $stmtInsertCategoria = $this->mysqli->prepare($queryInsertCategoria);

        foreach ($categorias as $categoria) {
            $idTipoPreferencia = $categoria["idTipoPreferencia"];
            $stmtInsertCategoria->bind_param('ii', $idActividad, $idTipoPreferencia);

            try {
                $stmtInsertCategoria->execute();
            } catch (PDOException $e) {
                echo "Error al insertar categoría de actividad: " . $e->getMessage();
                return false; // Fallo al insertar la categoría de actividad
            }
        }

        return true; // Éxito al insertar la actividad junto con sus categorías
    }


    /**
     * Modifica los datos de una actividad existente en la base de datos.
     *
     * @param int $idActividad ID de la actividad a modificar.
     * @param string $nombreActividad Nuevo nombre de la actividad.
     * @param string $descripcion Nueva descripción de la actividad.
     * @param int $duracion Nueva duración de la actividad.
     * @return bool true si la modificación fue exitosa, false si falló.
     */
    public function modificarActividad($idActividad, $nombreActividad, $descripcion, $duracion)
    {
        $query = "UPDATE Actividad SET nombreActividad = ?, descripcion = ?, duracion = ? WHERE idActividad = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('ssii', $nombreActividad, $descripcion, $duracion, $idActividad);

        try {
            $stmt->execute();
            return true; // Éxito al modificar la actividad
        } catch (PDOException $e) {
            echo "Error al modificar la actividad: " . $e->getMessage();
            return false; // Fallo al modificar la actividad
        }
    }

    /**
     * Elimina todas las filas de la tabla Actividad_tipoPreferencia asociadas a una actividad.
     *
     * @param int $idActividad El ID de la actividad cuyas filas se desean eliminar de la tabla Actividad_tipoPreferencia.
     * @return bool Devuelve true si las filas fueron eliminadas correctamente o no existían filas asociadas a la actividad, false en caso de error.
     */
    public function eliminarFilasActividadTipoPreferencia($idActividad)
    {
        // Consulta SQL para eliminar las filas de la tabla Actividad_tipoPreferencia
        $query = "DELETE FROM Actividad_tipoPreferencia WHERE idActividad = ?";

        // Preparar la consulta
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $idActividad);

        // Ejecutar la consulta y verificar si se ejecuta correctamente
        if ($stmt->execute()) {
            return true; // Se ejecutó la consulta sin errores
        } else {
            return false; // Ocurrió un error al ejecutar la consulta de eliminación
        }
    }

    /**
     * Modifica las preferencias asociadas a una actividad.
     *
     * @param int $idActividad El ID de la actividad a modificar.
     * @param array $categorias Las nuevas categorías asociadas a la actividad.
     * @return bool Devuelve true si la modificación se realizó correctamente, false en caso contrario.
     */
    public function modificarTipoPreferencias($idActividad, $categorias)
    {
        // Primero, eliminar todas las filas de la tabla Actividad_tipoPreferencia asociadas a la actividad
        if ($this->eliminarFilasActividadTipoPreferencia($idActividad)) {
            // Luego, insertar las nuevas preferencias asociadas a la actividad
            if ($this->insertarActividadConCategorias($idActividad, $categorias)) {
                return true; // La modificación se realizó correctamente
            } else {
                return false; // Ocurrió un error al insertar las nuevas preferencias
            }
        } else {
            return false; // Ocurrió un error al eliminar las preferencias existentes
        }
    }



    /**
     * Elimina todas las fotos de la galería asociadas a una actividad específica.
     *
     * @param int $idActividad El ID de la actividad de la cual se eliminarán las fotos de la galería.
     *
     * @return bool Retorna true si se eliminan las fotos de la galería exitosamente, o false en caso de error.
     */
    private function eliminarFotosGaleria($idActividad)
    {
        // Construir la consulta SQL para eliminar las fotos de la galería asociadas a la actividad
        $query = "DELETE FROM GaleriaFotos WHERE idActividad = ?";

        // Preparar la consulta SQL
        $stmt = $this->mysqli->prepare($query);

        // Vincular el parámetro de ID de actividad a la consulta preparada
        $stmt->bind_param('i', $idActividad);

        try {
            // Ejecutar la consulta SQL
            $stmt->execute();

            // Retornar true si se eliminan las fotos de la galería exitosamente
            return true;
        } catch (PDOException $e) {
            // Capturar cualquier excepción y manejarla adecuadamente
            echo "Error al eliminar las fotos de la galería: " . $e->getMessage();

            // Retornar false en caso de error durante la eliminación de las fotos de la galería
            return false;
        } finally {
            // Cerrar la conexión y liberar los recursos
            $stmt->close();
        }
    }

    /**
     * Elimina una actividad de la base de datos.
     *
     * @param int $idActividad ID de la actividad a eliminar.
     * @return bool true si la eliminación fue exitosa, false si falló.
     */
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

    /**
     * Elimina una foto de la galería de fotos.
     *
     * @param int $numImagen Número de la imagen a eliminar.
     * @return bool true si la eliminación fue exitosa, false si falló.
     */
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

    /**
     * Agrega una foto a la galería de una actividad.
     *
     * @param int $idActividad ID de la actividad a la que se agrega la foto.
     * @param string $foto URL de la foto a agregar.
     * @return int Retorna el ID de la foto agregada si la adición fue exitosa, de lo contrario, retorna -1.
     */
    public function agregarFotoGaleria($idActividad, $foto)
    {
        // Insertar una foto en la galería asociada a la actividad
        $query = "INSERT INTO GaleriaFotos (idActividad, url) VALUES (?, ?)";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('is', $idActividad, $foto);

        try {
            $stmt->execute();

            // Obtener el ID de la foto insertada
            $idFoto = $stmt->insert_id;

            return $idFoto; // Retorna el ID de la foto agregada
        } catch (PDOException $e) {
            echo "Error al agregar foto a la galería: " . $e->getMessage();
            return -1; // Fallo al agregar la foto a la galería
        }
    }

    /**
     * Busca actividades cuyo nombre coincide parcialmente con el nombre proporcionado.
     *
     * @param string $nombreActividad Parte del nombre de la actividad a buscar.
     * @return array Arreglo de actividades que coinciden con el nombre proporcionado.
     */
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
                'duracion' => $row['duracion'],
            );

            $actividades[] = $actividad;
        }

        return $actividades;
    }

    /**
     * Obtiene los detalles de una actividad por su ID.
     *
     * @param int $idActividad ID de la actividad.
     * @return ActividadSimple|null Objeto de la clase ActividadSimple si se encontró la actividad, null si no.
     */
    public function getActividad($idActividad)
    {
        $query = "SELECT idActividad, nombreActividad, descripcion, duracion FROM Actividad WHERE idActividad = ?";

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
                $fila['idActividad'],
                $fila['nombreActividad'],
                $fila['descripcion'],
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

    /**
     * Obtiene la galería de fotos asociada a una actividad por su ID.
     *
     * @param int $idActividad ID de la actividad.
     * @return array Arreglo de objetos Imagen que representan la galería de fotos de la actividad.
     */
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

    /**
     * Inserta un nuevo tipo de preferencia en la base de datos y crea la tabla correspondiente para sus preferencias hijas.
     *
     * @param string $tipoPreferencia Nombre del tipo de preferencia a insertar.
     * @return int|bool ID del tipo de preferencia insertado si la inserción fue exitosa, false si falló.
     */
    public function insertarTipoDePreferencia($tipoPreferencia)
    {
        // Insertar la preferencia en la tabla padre de tipoPreferencias
        $queryPreferenciaPadre = "INSERT INTO tipoPreferencias (tipoPreferencia) VALUES (?)";
        $stmtPreferenciaPadre = $this->mysqli->prepare($queryPreferenciaPadre);
        $stmtPreferenciaPadre->bind_param('s', $tipoPreferencia);

        try {
            $stmtPreferenciaPadre->execute();
            $idPreferenciaPadre = $stmtPreferenciaPadre->insert_id; // Obtener el ID de la preferencia insertada
            // Crear la tabla de preferencias hijas correspondiente
            //$this->crearTablaPreferencias($tipoPreferencia);
        } catch (PDOException $e) {
            echo "Error al insertar preferencia padre: " . $e->getMessage();
            return false; // Fallo al insertar la preferencia padre
        }

        return $idPreferenciaPadre; // Éxito al insertar la preferencia padre
    }

    /**
     * Función privada para crear la tabla de preferencias hijas correspondiente a un tipo de preferencia.
     *
     * @param string $tipoPreferencia Nombre del tipo de preferencia para el cual se creará la tabla.
     * @return void
     */
    private function crearTablaPreferencias($tipoPreferencia)
    {
        // Construir el nombre de la tabla
        $tabla = "Preferencias" . ucfirst($tipoPreferencia);

        // Consulta para crear la tabla con el campo idPreferencia autoincremental y clave primaria
        $queryCrearTabla = "CREATE TABLE $tabla (
        idPreferencia INT AUTO_INCREMENT PRIMARY KEY,
        nombrePreferencia VARCHAR(255),
        idTipoPreferencia INT,
        FOREIGN KEY (idTipoPreferencia) REFERENCES tipoPreferencias(idTipoPreferencia) ON DELETE CASCADE
    )";

        // Ejecutar la consulta para crear la tabla
        try {
            $this->mysqli->query($queryCrearTabla);
        } catch (PDOException $e) {
            echo "Error al crear la tabla de preferencias hijas: " . $e->getMessage();
        }
    }

    /**
     * Inserta una preferencia específica en la tabla correspondiente al tipo de preferencia.
     *
     * @param int $idTipoPreferencia ID del tipo de preferencia al que pertenece la preferencia.
     * @param string $tipoPreferencia Nombre del tipo de preferencia.
     * @param string $nombrePreferencia Nombre de la preferencia específica a insertar.
     * @return int Número distinto de -1 si la inserción fue exitosa, -1 si falló.
     */
    public function insertarPreferencia($idTipoPreferencia, $tipoPreferencia, $nombrePreferencia)
    {
        // Construir el nombre de la tabla a partir del tipo de preferencia
        $tabla = "Preferencias" . ucfirst($tipoPreferencia);

        // Preparar la consulta SQL para insertar la preferencia específica en la tabla correspondiente
        $query = "INSERT INTO $tabla (idTipoPreferencia, nombrePreferencia) VALUES (?, ?)";

        // Preparar la consulta
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('is', $idTipoPreferencia, $nombrePreferencia);

        try {
            // Ejecutar la consulta
            $stmt->execute();
            $idPreferencia = $stmt->insert_id; // Obtener el ID de la preferencia específica insertada
            return $idPreferencia;
        } catch (PDOException $e) {
            echo "Error al insertar preferencia específica: " . $e->getMessage();
            return -1; // Fallo al insertar la preferencia específica
        }
    }

    /**
     * Elimina un tipo de preferencia de la base de datos, incluida su tabla hija.
     *
     * @param int $idTipoPreferencia ID del tipo de preferencia a eliminar.
     * @return bool true si la eliminación fue exitosa, false si falló.
     */
    public function eliminarTipoPreferencia($idTipoPreferencia)
    {
        // Obtener el tipo de preferencia para construir el nombre de la tabla hija
        $queryTipoPreferencia = "SELECT tipoPreferencia FROM tipoPreferencias WHERE idTipoPreferencia = ?";
        $stmtTipoPreferencia = $this->mysqli->prepare($queryTipoPreferencia);
        $stmtTipoPreferencia->bind_param('i', $idTipoPreferencia);
        $stmtTipoPreferencia->execute();
        $stmtTipoPreferencia->bind_result($tipoPreferencia);
        $stmtTipoPreferencia->fetch();
        $stmtTipoPreferencia->close();

        // Construir el nombre de la tabla hija
        $nombreTablaHija = "Preferencias" . $tipoPreferencia;

        // Eliminar la preferencia padre
        $queryEliminarPadre = "DELETE FROM TipoPreferencias WHERE idTipoPreferencia = ?";
        $stmtEliminarPadre = $this->mysqli->prepare($queryEliminarPadre);
        $stmtEliminarPadre->bind_param('i', $idTipoPreferencia);

        // Ejecutar la eliminación de la preferencia padre
        try {
            $stmtEliminarPadre->execute();

            // Eliminar la tabla hija
            $queryEliminarTablaHija = "DROP TABLE IF EXISTS $nombreTablaHija";
            $this->mysqli->query($queryEliminarTablaHija);

            return true; // Éxito al eliminar la preferencia padre y su tabla hija
        } catch (PDOException $e) {
            echo "Error al eliminar la preferencia padre: " . $e->getMessage();
            return false; // Fallo al eliminar la preferencia padre
        }
    }

    /**
     * Elimina una preferencia específica de la base de datos.
     *
     * @param int $idPreferencia ID de la preferencia a eliminar.
     * @param string $tipoPreferencia Tipo de preferencia a la que pertenece la preferencia específica.
     * @return bool true si la eliminación fue exitosa, false si falló.
     */
    public function eliminarPreferencia($idPreferencia, $tipoPreferencia)
    {
        // Construir el nombre de la tabla de preferencia hija
        $tablaPreferencia = "Preferencias" . ucfirst($tipoPreferencia);

        // Preparar la sentencia para eliminar la fila de la preferencia hija
        $queryEliminarHija = "DELETE FROM $tablaPreferencia WHERE idPreferencia = ?";
        $stmtEliminarHija = $this->mysqli->prepare($queryEliminarHija);
        $stmtEliminarHija->bind_param('i', $idPreferencia);

        // Ejecutar la eliminación de la fila de la preferencia hija
        try {
            $stmtEliminarHija->execute();
            return true; // Éxito al eliminar la preferencia hija
        } catch (PDOException $e) {
            echo "Error al eliminar la preferencia hija: " . $e->getMessage();
            return false; // Fallo al eliminar la preferencia hija
        }
    }

    /**
     * Busca coincidencias de tipos de preferencias en la base de datos.
     *
     * @param string $tipoPreferencia Tipo de preferencia a buscar.
     * @return array Arreglo de tipos de preferencias encontrados.
     */
    public function buscarCoincidenciasTipoPreferencias($tipoPreferencia)
    {
        // Preparar la consulta SQL para buscar coincidencias de preferencias padre
        $sql = "SELECT * FROM tipoPreferencias WHERE tipoPreferencia LIKE CONCAT('%', ? , '%')";

        // Preparar la sentencia SQL
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $tipoPreferencia);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado de la consulta
        $result = $stmt->get_result();

        // Array para almacenar las preferencias padre encontradas
        $preferencias = array();

        // Recorrer el resultado y almacenar las preferencias encontradas en el array
        while ($row = $result->fetch_assoc()) {
            $preferencia = array(
                'idTipoPreferencia' => $row['idTipoPreferencia'],
                'tipoPreferencia' => $row['tipoPreferencia'],
            );

            $preferencias[] = $preferencia;
        }

        // Devolver el array de preferencias encontradas
        return $preferencias;
    }

    /**
     * Obtiene las preferencias específicas asociadas a un tipo de preferencia.
     *
     * @param int $idTipoPreferencia ID del tipo de preferencia.
     * @param string $tipoPreferencia Tipo de preferencia.
     * @return array|bool Arreglo de preferencias encontradas si la consulta fue exitosa, false si falló.
     */
    public function obtenerPreferencias($idTipoPreferencia, $tipoPreferencia)
    {
        // Inicializar el array de preferencias hijas
        $preferencias = array();

        // Construir el nombre de la tabla a partir del tipo de preferencia
        $tabla = "Preferencias" . ucfirst($tipoPreferencia);

        // Definir la consulta SQL genérica y el campo según el tipo de preferencia hija
        $sql = "SELECT idPreferencia, nombrePreferencia FROM $tabla WHERE idTipoPreferencia = ?";
        $campoId = 'idPreferencia';
        $campoNombre = 'nombrePreferencia';

        // Preparar la sentencia SQL
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $idTipoPreferencia);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Obtener el resultado de la consulta
            $result = $stmt->get_result();

            // Recorrer el resultado y guardar las preferencias hijas en el array
            while ($row = $result->fetch_assoc()) {
                $preferencia = array(
                    'idPreferencia' => $row[$campoId],
                    'nombrePreferencia' => $row[$campoNombre],
                    'idTipoPreferencia' => $idTipoPreferencia,
                    // Agregar otros campos según la estructura de tu tabla de preferencias hijas
                );
                $preferencias[] = $preferencia;
            }

            // Devolver el array de preferencias hijas
            return $preferencias;
        } else {
            // Si hay un error al ejecutar la consulta, devolver false
            return false;
        }
    }

    /**
     * Obtiene las filas de la tabla Actividad_tipopreferencia basadas en el ID de la actividad.
     *
     * @param int $idActividad El ID de la actividad cuyas filas se desean obtener de la tabla Actividad_tipopreferencia.
     * @return array|false Devuelve un array con los datos de las filas si se encuentran, o false si hay un error al ejecutar la consulta.
     */
    public function getCategoriasActividad($idActividad)
    {
        // Consulta SQL para obtener las filas de la tabla Actividad_tipopreferencia
        $query = "SELECT idActividad, idTipoPreferencia FROM Actividad_tipoPreferencia WHERE idActividad = ?";

        // Preparar la consulta
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $idActividad);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Obtener el resultado de la consulta
            $result = $stmt->get_result();

            // Crear un array para almacenar los datos de las filas
            $actividadTipoPreferenciaData = array();

            // Verificar si se encontraron filas
            if ($result->num_rows > 0) {
                // Iterar sobre las filas del resultado
                while ($row = $result->fetch_assoc()) {
                    // Agregar cada fila al array de datos
                    $actividadTipoPreferenciaData[] = array(
                        'idActividad' => $row['idActividad'],
                        'idTipoPreferencia' => $row['idTipoPreferencia'],
                        // Agregar otros campos según la estructura de tu tabla Actividad_tipoPreferencia
                    );
                }
            }

            // Devolver los datos de las filas
            return $actividadTipoPreferenciaData;
        } else {
            // Si hay un error al ejecutar la consulta, devolver false
            return false;
        }
    }


    /**
     * Obtiene todas las preferencias personales de un usuario a partir de su idUsuario.
     *
     * @param int $idUsuario El ID del usuario cuyas preferencias se desean obtener.
     * @return array|null|false Devuelve un array con los datos de las preferencias personales si se encuentran,
     *                          null si no se encuentran, o false si hay un error al ejecutar la consulta.
     */
    public function getPreferenciasUsuario($idUsuario)
    {
        // Consulta SQL para obtener las preferencias personales del usuario
        $query = "SELECT idUsuario, idTipoPreferencia, nombreTipoPreferencia, pInteres FROM usuarioPreferencias WHERE idUsuario = ?";

        // Preparar la consulta
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $idUsuario);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Obtener el resultado de la consulta
            $result = $stmt->get_result();

            // Verificar si se encontraron preferencias personales
            if ($result->num_rows > 0) {
                // Crear un array para almacenar todas las preferencias personales del usuario
                $preferenciasUsuario = array();

                // Iterar sobre cada fila del resultado
                while ($row = $result->fetch_assoc()) {
                    // Crear un array con los datos de la preferencia personal
                    $preferenciaUsuarioData = array(
                        'idUsuario' => $row['idUsuario'],
                        'idTipoPreferencia' => $row['idTipoPreferencia'],
                        'nombreTipoPreferencia' => $row['nombreTipoPreferencia'],
                        'pInteres' => $row['pInteres']
                        // Agregar otros campos según la estructura de tu tabla usuarioPreferencias
                    );

                    // Agregar la preferencia personal al array de preferencias del usuario
                    $preferenciasUsuario[] = $preferenciaUsuarioData;
                }

                // Devolver las preferencias personales del usuario
                return $preferenciasUsuario;
            } else {
                // Si no se encuentran preferencias personales, devolver null
                return null;
            }
        } else {
            // Si hay un error al ejecutar la consulta, devolver false
            return false;
        }
    }

    /**
     * Obtiene todas las filas de la tabla tipoPreferencias.
     *
     * @return array|false Devuelve un array con todas las filas de tipoPreferencias si se encuentran,
     *                    o false si hay un error al ejecutar la consulta.
     */
    public function getAllTipoPreferencias()
    {
        // Consulta SQL para obtener todas las filas de tipoPreferencias
        $query = "SELECT idTipoPreferencia, tipoPreferencia FROM tipoPreferencias";

        // Preparar la consulta
        $stmt = $this->mysqli->prepare($query);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Obtener el resultado de la consulta
            $result = $stmt->get_result();

            // Inicializar un array para almacenar todas las filas de tipoPreferencias
            $tipoPreferencias = array();

            // Recorrer el resultado y guardar las filas en el array
            while ($row = $result->fetch_assoc()) {
                $tipoPreferenciaData = array(
                    'idTipoPreferencia' => $row['idTipoPreferencia'],
                    'tipoPreferencia' => $row['tipoPreferencia'],
                    // Agregar otros campos según la estructura de tu tabla tipoPreferencias
                );
                $tipoPreferencias[] = $tipoPreferenciaData;
            }

            // Devolver todas las filas de tipoPreferencias
            return $tipoPreferencias;
        } else {
            // Si hay un error al ejecutar la consulta, devolver false
            return false;
        }
    }

    /**
     * Inserta una preferencia personal para un usuario en la tabla UsuarioPreferencias.
     *
     * @param int $idUsuario ID del usuario al que se asociará la preferencia personal.
     * @param int $nombreTipoPreferencia NOMBRE de la preferencia personal.
     * @param int $idTipoPreferencia ID del tipo de preferencia.
     * @return bool Devuelve true si la inserción fue exitosa
     * */
    public function insertarPreferenciaPersonal($idUsuario, $nombreTipoPreferencia, $idTipoPreferencia)
    {
        // Insertar la relación en la tabla UsuarioPreferencias
        $queryUsuarioPreferencias = "INSERT INTO UsuarioPreferencias (idUsuario, nombreTipoPreferencia, idTipoPreferencia) VALUES (?, ?, ?)";
        $stmtUsuarioPreferencias = $this->mysqli->prepare($queryUsuarioPreferencias);
        $stmtUsuarioPreferencias->bind_param('isi', $idUsuario, $nombreTipoPreferencia, $idTipoPreferencia);

        try {
            $stmtUsuarioPreferencias->execute(); // Ejecutar la consulta de inserción en UsuarioPreferencias
            //$idUsuarioPreferencia = $stmtUsuarioPreferencias->insert_id; // Obtener el ID de la preferencia personal insertada
            return true;
        } catch (PDOException $e) {
            echo "Error al insertar preferencia personal: " . $e->getMessage(); // Manejar cualquier excepción que pueda ocurrir durante la inserción
            return false; // Retornar un valor negativo en caso de error
        }
    }

    /**
     * Elimina una preferencia personal de un usuario de la base de datos.
     *
     * @param int $idUsuario El ID del usuario cuya preferencia personal se desea eliminar.
     * @param string $nombreTipoPreferencia El nombre de la preferencia personal que se desea eliminar.
     * @param int $idTipoPreferencia El ID del tipo de preferencia asociado a la preferencia personal.
     * @return bool Devuelve true si la preferencia personal fue eliminada correctamente, false en caso contrario.
     */
    public function eliminarPreferenciaPersonal($idUsuario, $nombreTipoPreferencia, $idTipoPreferencia)
    {
        // Consulta SQL para eliminar la preferencia personal de la tabla UsuarioPreferencias
        $query = "DELETE FROM UsuarioPreferencias WHERE idUsuario = ? AND nombreTipoPreferencia = ? AND idTipoPreferencia = ?";

        // Preparar la consulta
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('isi', $idUsuario, $nombreTipoPreferencia, $idTipoPreferencia);

        // Ejecutar la consulta y verificar si se ejecuta correctamente
        if ($stmt->execute()) {
            // Verificar si se eliminó alguna fila
            if ($stmt->affected_rows > 0) {
                return true; // La preferencia personal fue eliminada correctamente
            } else {
                return false; // No se eliminó ninguna fila, probablemente los datos proporcionados no coinciden con ninguna preferencia personal existente
            }
        } else {
            return false; // Ocurrió un error al ejecutar la consulta de eliminación
        }
    }

    /**
     * Elimina todas las preferencias personales asociadas a un usuario de la base de datos.
     *
     * @param int $idUsuario El ID del usuario cuyas preferencias personales se desean eliminar.
     * @return bool Devuelve true si las preferencias personales del usuario fueron eliminadas correctamente, false si no se eliminó ninguna fila o si hay un error al ejecutar la consulta.
     */
    public function eliminarPreferenciasPersonalesUsuario($idUsuario)
    {
        // Consulta SQL para eliminar las preferencias personales del usuario de la tabla UsuarioPreferencias
        $query = "DELETE FROM usuariopreferencias WHERE idUsuario = ?";

        // Preparar la consulta
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $idUsuario);

        // Ejecutar la consulta y verificar si se ejecuta correctamente
        if ($stmt->execute()) {
            // Verificar si se eliminó alguna fila
            if ($stmt->affected_rows > 0) {
                return true; // Las preferencias personales del usuario fueron eliminadas correctamente
            } else {
                return false; // No se eliminó ninguna fila, probablemente el usuario no tenía preferencias personales asociadas
            }
        } else {
            return false; // Ocurrió un error al ejecutar la consulta de eliminación
        }
    }

    /**
     * Actualiza las preferencias personales de un usuario en la base de datos.
     *
     * Este método elimina todas las preferencias personales existentes del usuario y luego inserta las nuevas preferencias proporcionadas.
     *
     * @param int $idUsuario El ID del usuario cuyas preferencias personales se van a actualizar.
     * @param array $preferencias Un array que contiene las nuevas preferencias personales del usuario.
     * @return void
     */
    public function actualizarPreferenciasPersonales($idUsuario, $preferencias)
    {
        // Eliminar todas las preferencias personales del usuario
        $this->eliminarPreferenciasPersonalesUsuario($idUsuario);

        // Insertar las nuevas preferencias personales del usuario
        foreach ($preferencias as $preferencia) {
            $nombreTipoPreferencia = $preferencia["nombreTipoPreferencia"];
            $idTipoPreferencia = $preferencia["idTipoPreferencia"];
            $this->insertarPreferenciaPersonal($idUsuario, $nombreTipoPreferencia, $idTipoPreferencia);
        }
    }



    /**
     * Función para recomendar actividades personalizadas para un usuario.
     *
     * Esta función recomienda actividades personalizadas para un usuario basadas en sus preferencias personales.
     *
     * @param int $idUsuario El ID del usuario para el cual se recomendarán las actividades.
     * @return array Un array de actividades recomendadas para el usuario.
     */
    function recomendarActividadesPersonalizadas($idUsuario)
    {
        // Obtener las preferencias personales del usuario
        $preferenciasUsuario = $this->getPreferenciasUsuario($idUsuario);

        //echo var_dump($preferenciasUsuario);

        // Recomendar actividades personalizadas para el usuario basadas en sus preferencias
        $actividadesRecomendadas = $this->recomendarActividades($preferenciasUsuario);

        //echo var_dump($actividadesRecomendadas);

        // Devolver las actividades recomendadas
        return $actividadesRecomendadas;
    }

    /**
     * Función para recomendar actividades personalizadas basadas en las preferencias del usuario.
     *
     * Esta función recomienda actividades personalizadas basadas en las preferencias del usuario.
     *
     * @param array $preferenciasUsuario Un array que contiene las preferencias personales del usuario.
     * @return array Un array de actividades recomendadas basadas en las preferencias del usuario.
     */
    function recomendarActividades($preferenciasUsuario)
    {

        // Si el usuario tiene todas sus preferencias personales a 0 entonces no tiene preferencias 
        $sumaPuntosInteres = 0;
        // Recorrer el array de preferencias y sumar los puntos de interés
        foreach ($preferenciasUsuario as $preferencia) {
            $sumaPuntosInteres += $preferencia['pInteres'];
        }

        // Verificar si la suma es igual a cero
        $sinPreferencias = ($sumaPuntosInteres == 0);

        // Verificar si el usuario tiene preferencias personales
        if (empty($preferenciasUsuario) or $sinPreferencias) {
            // Si el usuario no tiene preferencias, obtener todas las actividades de la base de datos
            $query = "SELECT idActividad, nombreActividad, descripcion, duracion FROM actividad";
            $stmt = $this->mysqli->prepare($query);

            // Verificar si la preparación de la consulta fue exitosa
            if ($stmt) {
                // Ejecutar la consulta
                $stmt->execute();

                // Obtener el resultado de la consulta
                $result = $stmt->get_result();

                // Crear un array para almacenar las actividades recomendadas
                $actividadesRecomendadas = [];

                // Verificar si se obtuvieron resultados
                if ($result->num_rows > 0) {
                    // Iterar sobre cada fila del resultado
                    while ($row = $result->fetch_assoc()) {
                        // Obtener las fotos de la galería asociadas a la actividad
                        $queryGaleria = "SELECT url FROM GaleriaFotos WHERE idActividad = ?";
                        $stmtGaleria = $this->mysqli->prepare($queryGaleria);
                        $stmtGaleria->bind_param('i', $row['idActividad']);
                        $stmtGaleria->execute();
                        $resultGaleria = $stmtGaleria->get_result();

                        // Crear un array para almacenar las URLs de las fotos
                        $fotos = [];
                        while ($foto = $resultGaleria->fetch_assoc()) {
                            $fotos[] = $foto['url'];
                        }

                        // Agregar las fotos al array de la actividad
                        $row['fotos'] = $fotos;

                        // Agregar la fila al array de actividades recomendadas
                        $actividadesRecomendadas[] = $row;
                    }
                }

                // Cerrar la consulta preparada
                $stmt->close();

                // Devolver las actividades recomendadas
                return $actividadesRecomendadas;
            } else {
                // Si la preparación de la consulta falla, devolver un array vacío
                return [];
            }
        } else {
            // Consulta SQL para obtener todas las filas de la tabla actividad_tipoPreferencia
            $query = "SELECT idActividad, idTipoPreferencia FROM actividad_tipoPreferencia";
            $stmt = $this->mysqli->prepare($query);

            // Verificar si la preparación de la consulta fue exitosa
            if ($stmt) {
                // Ejecutar la consulta
                $stmt->execute();

                // Obtener el resultado de la consulta
                $result = $stmt->get_result();

                // Crear un array asociativo para almacenar las puntuaciones de las actividades
                $puntuacionActividades = [];

                // Inicializar la puntuación de todas las actividades en 0
                while ($row = $result->fetch_assoc()) {
                    $idActividad = $row['idActividad'];
                    $puntuacionActividades[$idActividad] = 0;
                }

                // Reiniciar el puntero del resultado para recorrerlo nuevamente
                $result->data_seek(0);

                // Calcular la puntuación de cada actividad
                while ($row = $result->fetch_assoc()) {
                    $idActividad = $row['idActividad'];
                    foreach ($preferenciasUsuario as $preferenciaUsuario) {
                        if ($preferenciaUsuario['idTipoPreferencia'] == $row['idTipoPreferencia']) {

                            $nuevaPuntuacion = $preferenciaUsuario['pInteres'];
                            $puntuacionActividades[$idActividad] = $nuevaPuntuacion;
                        }
                    }
                }

                // Ordenar las actividades según su puntuación de forma descendente
                arsort($puntuacionActividades);

                // Obtener las actividades recomendadas basadas en la puntuación
                $actividadesRecomendadas = [];

                foreach ($puntuacionActividades as $idActividad => $puntuacion) {

                    if ($puntuacion > 0) {
                        // Si la actividad tiene al menos una coincidencia de preferencia, consultar su información
                        $query = "SELECT idActividad, nombreActividad, descripcion, duracion FROM actividad WHERE idActividad = ?";
                        $stmt = $this->mysqli->prepare($query);
                        $stmt->bind_param('i', $idActividad);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $actividad = $result->fetch_assoc();

                        // Obtener las fotos de la galería asociadas a la actividad
                        $queryGaleria = "SELECT url FROM GaleriaFotos WHERE idActividad = ?";
                        $stmtGaleria = $this->mysqli->prepare($queryGaleria);
                        $stmtGaleria->bind_param('i', $idActividad);
                        $stmtGaleria->execute();
                        $resultGaleria = $stmtGaleria->get_result();

                        // Crear un array para almacenar las URLs de las fotos
                        $fotos = [];
                        while ($foto = $resultGaleria->fetch_assoc()) {
                            $fotos[] = $foto['url'];
                        }

                        // Agregar las fotos al array de la actividad
                        $actividad['fotos'] = $fotos;

                        // Agregar la actividad a la lista de recomendaciones
                        $actividadesRecomendadas[] = $actividad;
                    }
                }

                // Devolver las actividades recomendadas
                return $actividadesRecomendadas;
            } else {
                // Si la preparación de la consulta falla, devolver un array vacío
                return [];
            }
        }
    }

    /**
     * Actualiza los puntos de interés de las preferencias del usuario
     * basado en la aceptación o rechazo de una actividad.
     *
     * @param array $preferenciasActividades Un array de preferencias asociadas a la actividad.
     * @param array $preferenciasPersonales Un array de preferencias personales del usuario.
     * @param string $estado El estado de la actividad ("aceptada" o "rechazada").
     * @return void
     */
    function actualizarPuntosInteres($preferenciasActividades, $preferenciasPersonales, $estado)
    {

        // Verificar si el estado es "aceptada" o "rechazada"
        if ($estado === "aceptada") {
            $puntos = 1; // Si es aceptada, sumar un punto
        } elseif ($estado === "rechazada") {
            $puntos = -1; // Si es rechazada, restar un punto
        } else {
            // Estado no válido, no hacer nada
            return;
        }

        // Preparar y ejecutar consultas de actualización para las preferencias del usuario que coincidan con las preferencias de la actividad
        foreach ($preferenciasActividades as $preferenciaActividad) {
            $idTipoPreferencia = $preferenciaActividad['idTipoPreferencia'];

            // Buscar la preferencia del usuario asociada a la preferencia de la actividad
            foreach ($preferenciasPersonales as $preferenciaPersonal) {
                if ($preferenciaPersonal['idTipoPreferencia'] === $idTipoPreferencia) {
                    $pInteresActualizado = $preferenciaPersonal['pInteres'] + $puntos;

                    // Consulta de actualización
                    $query = "UPDATE usuariopreferencias SET pInteres = ? WHERE idTipoPreferencia = ?";
                    $stmt = $this->mysqli->prepare($query);
                    $stmt->bind_param('ii', $pInteresActualizado, $idTipoPreferencia);
                    $stmt->execute();
                }
            }
        }
    }
}
