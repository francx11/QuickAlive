<?php
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

    public function __construct($nickName, $telefono, $correo, $password, $nombre, $apellidos, $edad, $rol)
    {
        $this->nickName = $nickName;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->password = $password;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->edad = $edad;
        $this->rol = $rol;
    }

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function getNickName()
    {
        return $this->nickName;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellidos()
    {
        return $this->apellidos;
    }

    public function getEdad()
    {
        return $this->edad;
    }

    public function getRol()
    {
        return $this->rol;
    }

}
