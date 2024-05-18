<?php

/**
 * Clase base para representar una actividad.
 */
class Actividad
{
    private $idActividad;
    private $nombreActividad;
    private $descripcion;
    private $duracion;
    private $tipoActividad;
    private $galeriaFotos = [];
    private $categorias = [];

    /**
     * Constructor de la clase Actividad.
     *
     * @param string $nombreActividad Nombre de la actividad.
     * @param string $descripcion Descripción de la actividad.
     * @param string $tipoActividad Tipo de la actividad.
     * @param string $subTipoActividad SubTipo de la actividad.
     * @param int $duracion Duración de la actividad en minutos.
     */
    public function __construct($idActividad, $nombreActividad, $descripcion, $duracion, $tipoActividad)
    {
        $this->idActividad = $idActividad;
        $this->nombreActividad = $nombreActividad;
        $this->descripcion = $descripcion;
        $this->duracion = $duracion;
        $this->tipoActividad = $tipoActividad;
        $this->categorias = [];
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
     * Obtiene la duración de la actividad en minutos.
     *
     * @return int Duración de la actividad en minutos.
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * Obtiene el tipo de actividad de una actividad.
     *
     * @return string tipo de actividad.
     */
    public function getTipoActividad()
    {
        return $this->tipoActividad;
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
     * Obtiene las categorias asociadas a la actividad.
     *
     * @return array Categorias asociada a la actividad.
     */
    public function getCategorias()
    {
        return $this->categorias;
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
     * Establece la duración de la actividad en minutos.
     *
     * @param int $duracion Duración de la actividad en minutos.
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;
    }

    /**
     * Establece el tipo de una actividads
     *
     * @return array Galería de fotos asociada a la actividad.
     */
    public function setTipoActividad($tipoActividad)
    {
        $this->tipoActividad = $tipoActividad;
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

    /**
     * Añade una categorías a la actividad.
     *
     * @param string $categoría a añadir.
     */
    public function aniadirCategoría($categoría)
    {
        $this->categorias[] = $categoría;
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
     * @param int $duracion Duración de la actividad en minutos.
     */
    public function __construct($idActividad, $nombreActividad, $descripcion, $duracion, $tipoActividad)
    {
        parent::__construct($idActividad, $nombreActividad, $descripcion, $duracion, $tipoActividad);
    }
}
/**
 * Clase para representar una actividad geolocalizable, que hereda de la clase Actividad.
 */
class ActividadGeolocalizable extends Actividad
{
    /**
     * @var string URL de la imagen remota.
     */
    private $urlRemota;

    /**
     * @var string ID de la actividad en la API externa.
     */
    private $idApi;

    /**
     * @var string Fecha límite para realizar la actividad.
     */
    private $fechaLimite;

    /**
     * Constructor de la clase ActividadGeolocalizable.
     *
     * @param int $idActividad ID de la actividad.
     * @param string $nombreActividad Nombre de la actividad.
     * @param string $descripcion Descripción de la actividad.
     * @param int $duracion Duración de la actividad en minutos.
     * @param string $urlRemota URL de la imagen remota.
     * @param string $idApi ID de la actividad en la API externa.
     * @param string $fechaLimite Fecha límite para realizar la actividad (formato 'YYYY-MM-DD').
     */
    public function __construct($idActividad, $nombreActividad, $descripcion, $duracion, $tipoActividad, $urlRemota, $idApi, $fechaLimite)
    {
        parent::__construct($idActividad, $nombreActividad, $descripcion, $duracion, $tipoActividad);
        $this->urlRemota = $urlRemota;
        $this->idApi = $idApi;
        $this->fechaLimite = $fechaLimite;
    }

    /**
     * Obtiene la URL de la imagen remota.
     *
     * @return string URL de la imagen remota.
     */
    public function getUrlRemota()
    {
        return $this->urlRemota;
    }

    /**
     * Establece la URL de la imagen remota.
     *
     * @param string $urlRemota URL de la imagen remota.
     */
    public function setUrlRemota($urlRemota)
    {
        $this->urlRemota = $urlRemota;
    }

    /**
     * Obtiene el ID de la actividad en la API externa.
     *
     * @return string ID de la actividad en la API externa.
     */
    public function getIdApi()
    {
        return $this->idApi;
    }

    /**
     * Establece el ID de la actividad en la API externa.
     *
     * @param string $idApi ID de la actividad en la API externa.
     */
    public function setIdApi($idApi)
    {
        $this->idApi = $idApi;
    }

    /**
     * Obtiene la fecha límite para realizar la actividad.
     *
     * @return string Fecha límite para realizar la actividad.
     */
    public function getFechaLimite()
    {
        return $this->fechaLimite;
    }

    /**
     * Establece la fecha límite para realizar la actividad.
     *
     * @param string $fechaLimite Fecha límite para realizar la actividad (formato 'YYYY-MM-DD').
     */
    public function setFechaLimite($fechaLimite)
    {
        $this->fechaLimite = $fechaLimite;
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
