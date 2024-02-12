<?php
// Clase base para representar una actividad
class Actividad
{
    protected $idActividad;
    protected $nombreActividad;
    protected $descripcion;
    protected $tipoActividad;
    protected $duracion;
    protected $completada;

    public function __construct($nombreActividad, $descripcion, $tipoActividad, $duracion, $completada)
    {
        $this->nombreActividad = $nombreActividad;
        $this->descripcion = $descripcion;
        $this->tipoActividad = $tipoActividad;
        $this->duracion = $duracion;
        $this->completada = $completada;
    }

    public function getNombreActividad()
    {
        return $this->nombreActividad;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getTipoActividad()
    {
        return $this->tipoActividad;
    }

    public function getDuracion()
    {
        return $this->duracion;
    }

    public function getCompletada()
    {
        return $this->completada;
    }

    public function setNombreActividad($nombreActividad)
    {
        $this->nombreActividad = $nombreActividad;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function setTipoActividad($tipoActividad)
    {
        $this->tipoActividad = $tipoActividad;
    }

    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;
    }

    public function setCompletada($completada)
    {
        $this->completada = $completada;
    }
}

// Clase para representar una actividad simple
class ActividadSimple extends Actividad
{
    private $galeriaFotos;

    public function __construct($nombreActividad, $descripcion, $tipoActividad, $duracion, $completada, $galeriaFotos)
    {
        parent::__construct($nombreActividad, $descripcion, $tipoActividad, $duracion, $completada);
        $this->galeriaFotos = $galeriaFotos;
    }

    // Método getter para la galería de fotos
    public function getGaleriaFotos()
    {
        return $this->galeriaFotos;
    }

    // Método setter para la galería de fotos
    public function setGaleriaFotos($galeriaFotos)
    {
        $this->galeriaFotos = $galeriaFotos;
    }
}

// Clase para representar una foto en la galería de fotos
class GaleriaFotos
{
    private $url;
    private $idActividad;

    public function __construct($idActividad, $url)
    {
        $this->$idActividad = $idActividad;
        $this->url = $url;
    }

    public function getIdActividad()
    {
        return $this->$idActividad;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }
}
