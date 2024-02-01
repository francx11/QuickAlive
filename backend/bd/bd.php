<?php

class BD
{
    public $mysqli;

    public function getConexion()
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
}
