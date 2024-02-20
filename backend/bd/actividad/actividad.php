<?php
// Clase base para representar una actividad
class Actividad
{
    public $idActividad;
    public $nombreActividad;
    public $descripcion;
    public $tipoActividad;
    public $duracion;
    public $galeriaFotos = [];

    public function __construct($nombreActividad, $descripcion, $tipoActividad, $duracion)
    {
        $this->nombreActividad = $nombreActividad;
        $this->descripcion = $descripcion;
        $this->tipoActividad = $tipoActividad;
        $this->duracion = $duracion;
        $this->galeriaFotos = [];

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

    public function getGaleriaFotos()
    {
        return $this->galeriaFotos;
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

    public function aniadirFotosGaleria($imagen)
    {

        $this->galeriaFotos[] = $imagen;

    }
}

// Clase para representar una actividad simple
class ActividadSimple extends Actividad
{

    public function __construct($nombreActividad, $descripcion, $tipoActividad, $duracion)
    {
        parent::__construct($nombreActividad, $descripcion, $tipoActividad, $duracion);

    }

}

// Clase para representar una actividad geolocalizable
class ActividadGeolocalizable extends Actividad
{
    private $ubicacion;
    private $fechaRealizacion;

    public function __construct($nombreActividad, $descripcion, $tipoActividad, $duracion, $completada, $galeriaFotos, $ubicacion, $fechaRealizacion)
    {
        parent::__construct($nombreActividad, $descripcion, $tipoActividad, $duracion, $completada, $galeriaFotos);
        $this->ubicacion = $ubicacion;
        $this->fechaRealizacion = $fechaRealizacion;
    }

    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    public function getFechaRealizacion()
    {
        return $this->fechaRealizacion;
    }

    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;
    }

    public function setFechaRealizacion($fechaRealizacion)
    {
        $this->fechaRealizacion = $fechaRealizacion;
    }
}

// Clase para representar una foto en la galería de fotos

class Imagen
{
    public $numImagen;
    public $url; // Cambiado a público para que sea accesible desde fuera de la clase
    public $idActividad;

    public function __construct($numImagen, $idActividad, $url)
    {
        $this->numImagen = $numImagen;
        $this->idActividad = $idActividad;
        $this->url = $url;
    }

    public function getNumImagen()
    {
        return $this->numImagen; // Corregido para acceder a la propiedad $numImagen correctamente
    }

    public function getIdActividad()
    {
        return $this->idActividad; // Corregido para acceder a la propiedad $idActividad correctamente
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
