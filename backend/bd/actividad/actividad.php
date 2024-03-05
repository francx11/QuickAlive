<?php
/**
 * Clase base para representar una actividad.
 */
class Actividad
{
    private $idActividad;
    private $nombreActividad;
    private $descripcion;
    private $tipoActividad;
    private $duracion;
    private $galeriaFotos = [];

    /**
     * Constructor de la clase Actividad.
     *
     * @param string $nombreActividad Nombre de la actividad.
     * @param string $descripcion Descripción de la actividad.
     * @param string $tipoActividad Tipo de la actividad.
     * @param int $duracion Duración de la actividad en minutos.
     */
    public function __construct($idActividad, $nombreActividad, $descripcion, $tipoActividad, $duracion)
    {
        $this->idActividad = $idActividad;
        $this->nombreActividad = $nombreActividad;
        $this->descripcion = $descripcion;
        $this->tipoActividad = $tipoActividad;
        $this->duracion = $duracion;
        $this->galeriaFotos = [];
    }

    /**
     * Obtiene el id de la actividad.
     *
     * @return int Id de la actividad.
     */
    public function getIdActividad()
    {
        return $this->idActividad;
    }

    /**
     * Obtiene el nombre de la actividad.
     *
     * @return string Nombre de la actividad.
     */
    public function getNombreActividad()
    {
        return $this->nombreActividad;
    }

    /**
     * Obtiene la descripción de la actividad.
     *
     * @return string Descripción de la actividad.
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Obtiene el tipo de la actividad.
     *
     * @return string Tipo de la actividad.
     */
    public function getTipoActividad()
    {
        return $this->tipoActividad;
    }

    /**
     * Obtiene la duración de la actividad en minutos.
     *
     * @return int Duración de la actividad en minutos.
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * Obtiene la galería de fotos asociada a la actividad.
     *
     * @return array Galería de fotos asociada a la actividad.
     */
    public function getGaleriaFotos()
    {
        return $this->galeriaFotos;
    }

    /**
     * Establece el nombre de la actividad.
     *
     * @param string $nombreActividad Nombre de la actividad.
     */
    public function setNombreActividad($nombreActividad)
    {
        $this->nombreActividad = $nombreActividad;
    }

    /**
     * Establece la descripción de la actividad.
     *
     * @param string $descripcion Descripción de la actividad.
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * Establece el tipo de la actividad.
     *
     * @param string $tipoActividad Tipo de la actividad.
     */
    public function setTipoActividad($tipoActividad)
    {
        $this->tipoActividad = $tipoActividad;
    }

    /**
     * Establece la duración de la actividad en minutos.
     *
     * @param int $duracion Duración de la actividad en minutos.
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;
    }

    /**
     * Añade una foto a la galería de fotos asociada a la actividad.
     *
     * @param string $imagen URL de la imagen a añadir.
     */
    public function aniadirFotosGaleria($imagen)
    {
        $this->galeriaFotos[] = $imagen;
    }
}

/**
 * Clase para representar una actividad simple, que hereda de la clase Actividad.
 */
class ActividadSimple extends Actividad
{
    /**
     * Constructor de la clase ActividadSimple.
     *
     * @param string $nombreActividad Nombre de la actividad.
     * @param string $descripcion Descripción de la actividad.
     * @param string $tipoActividad Tipo de la actividad.
     * @param int $duracion Duración de la actividad en minutos.
     */
    public function __construct($idActividad, $nombreActividad, $descripcion, $tipoActividad, $duracion)
    {
        parent::__construct($idActividad, $nombreActividad, $descripcion, $tipoActividad, $duracion);
    }
}

/**
 * Clase para representar una actividad geolocalizable, que hereda de la clase Actividad.
 */
class ActividadGeolocalizable extends Actividad
{
    private $ubicacion;
    private $fechaRealizacion;

    /**
     * Constructor de la clase ActividadGeolocalizable.
     *
     * @param string $nombreActividad Nombre de la actividad.
     * @param string $descripcion Descripción de la actividad.
     * @param string $tipoActividad Tipo de la actividad.
     * @param int $duracion Duración de la actividad en minutos.
     * @param bool $completada Estado de la actividad (completada o no).
     * @param array $galeriaFotos Galería de fotos asociadas a la actividad.
     * @param string $ubicacion Ubicación geográfica de la actividad.
     * @param string $fechaRealizacion Fecha de realización de la actividad.
     */
    public function __construct($nombreActividad, $descripcion, $tipoActividad, $duracion, $completada, $galeriaFotos, $ubicacion, $fechaRealizacion)
    {
        parent::__construct($nombreActividad, $descripcion, $tipoActividad, $duracion, $completada, $galeriaFotos);
        $this->ubicacion = $ubicacion;
        $this->fechaRealizacion = $fechaRealizacion;
    }

    /**
     * Obtiene la ubicación geográfica de la actividad.
     *
     * @return string Ubicación geográfica de la actividad.
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    /**
     * Obtiene la fecha de realización de la actividad.
     *
     * @return string Fecha de realización de la actividad.
     */
    public function getFechaRealizacion()
    {
        return $this->fechaRealizacion;
    }

    /**
     * Establece la ubicación geográfica de la actividad.
     *
     * @param string $ubicacion Ubicación geográfica de la actividad.
     */
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;
    }

    /**
     * Establece la fecha de realización de la actividad.
     *
     * @param string $fechaRealizacion Fecha de realización de la actividad.
     */
    public function setFechaRealizacion($fechaRealizacion)
    {
        $this->fechaRealizacion = $fechaRealizacion;
    }
}

/**
 * Clase para representar una imagen en la galería de fotos asociada a una actividad.
 */
class Imagen
{
    public $numImagen;
    public $url;
    public $idActividad;

    /**
     * Constructor de la clase Imagen.
     *
     * @param int $numImagen Número de la imagen en la galería.
     * @param int $idActividad ID de la actividad a la que pertenece la imagen.
     * @param string $url URL de la imagen.
     */
    public function __construct($numImagen, $idActividad, $url)
    {
        $this->numImagen = $numImagen;
        $this->idActividad = $idActividad;
        $this->url = $url;
    }

    /**
     * Obtiene el número de la imagen en la galería.
     *
     * @return int Número de la imagen en la galería.
     */
    public function getNumImagen()
    {
        return $this->numImagen;
    }

    /**
     * Obtiene el ID de la actividad a la que pertenece la imagen.
     *
     * @return int ID de la actividad a la que pertenece la imagen.
     */
    public function getIdActividad()
    {
        return $this->idActividad;
    }

    /**
     * Obtiene la URL de la imagen.
     *
     * @return string URL de la imagen.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Establece la URL de la imagen.
     *
     * @param string $url URL de la imagen.
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
