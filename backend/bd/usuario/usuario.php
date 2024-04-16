<?php
/**
 * Clase que representa a un usuario del sistema.
 */
class Usuario
{
    private $idUsuario;
    private $nickName;
    private $telefono;
    private $correo;
    private $password;
    private $nombre;
    private $apellidos;
    private $edad;
    private $rol;

    /**
     * Constructor de la clase Usuario.
     *
     * @param string $nickName Nombre de usuario (nickName) del usuario.
     * @param string $telefono Número de teléfono del usuario.
     * @param string $correo Correo electrónico del usuario.
     * @param string $password Contraseña del usuario.
     * @param string $nombre Nombre del usuario.
     * @param string $apellidos Apellidos del usuario.
     * @param int $edad Edad del usuario.
     * @param string $rol Rol del usuario en el sistema.
     */
    public function __construct($idUsuario, $nickName, $telefono, $correo, $password, $nombre, $apellidos, $edad, $rol)
    {
        $this->idUsuario = $idUsuario;
        $this->nickName = $nickName;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->password = $password;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->edad = $edad;
        $this->rol = $rol;
    }

    /**
     * Obtiene el ID del usuario.
     *
     * @return int ID del usuario.
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Obtiene el nombre de usuario (nickName) del usuario.
     *
     * @return string Nombre de usuario (nickName) del usuario.
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * Obtiene el número de teléfono del usuario.
     *
     * @return string Número de teléfono del usuario.
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Obtiene el correo electrónico del usuario.
     *
     * @return string Correo electrónico del usuario.
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Obtiene la contraseña del usuario.
     *
     * @return string Contraseña del usuario.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Obtiene el nombre del usuario.
     *
     * @return string Nombre del usuario.
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Obtiene los apellidos del usuario.
     *
     * @return string Apellidos del usuario.
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Obtiene la edad del usuario.
     *
     * @return int Edad del usuario.
     */
    public function getEdad()
    {
        return $this->edad;
    }

    /**
     * Obtiene el rol del usuario en el sistema.
     *
     * @return string Rol del usuario en el sistema.
     */
    public function getRol()
    {
        return $this->rol;
    }
}
