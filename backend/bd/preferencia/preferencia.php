<?php

// Clase padre Preferencia
class Preferencia
{
    protected $idPreferencia;
    protected $tipoPreferencia;

    public function __construct($idPreferencia, $tipoPreferencia)
    {
        $this->idPreferencia = $idPreferencia;
        $this->tipoPreferencia = $tipoPreferencia;
    }

    public function getIdPreferencia()
    {
        return $this->idPreferencia;
    }

    public function getTipoPreferencia()
    {
        return $this->tipoPreferencia;
    }
}

// Clase hija PreferenciasDeportivas
class PreferenciasDeportivas extends Preferencia
{
    protected $nombreDeporte;

    public function __construct($idPreferencia, $tipoPreferencia, $nombreDeporte)
    {
        parent::__construct($idPreferencia, $tipoPreferencia);
        $this->nombreDeporte = $nombreDeporte;
    }

    public function getNombreDeporte()
    {
        return $this->nombreDeporte;
    }
}

// Clase hija PreferenciaCultural
class PreferenciaCultural extends Preferencia
{
    protected $nombreActividadCultural;

    public function __construct($idPreferencia, $tipoPreferencia, $nombreActividadCultural)
    {
        parent::__construct($idPreferencia, $tipoPreferencia);
        $this->nombreActividadCultural = $nombreActividadCultural;
    }

    public function getNombreActividadCultural()
    {
        return $this->nombreActividadCultural;
    }
}

// Clase hija PreferenciaEntretenimiento
class PreferenciaEntretenimiento extends Preferencia
{
    protected $nombreFormaEntretenimiento;

    public function __construct($idPreferencia, $tipoPreferencia, $nombreFormaEntretenimiento)
    {
        parent::__construct($idPreferencia, $tipoPreferencia);
        $this->nombreFormaEntretenimiento = $nombreFormaEntretenimiento;
    }

    public function getNombreFormaEntretenimiento()
    {
        return $this->nombreFormaEntretenimiento;
    }
}

// Clase hija PreferenciaNaturaleza
class PreferenciaNaturaleza extends Preferencia
{
    protected $nombreActividadNatural;

    public function __construct($idPreferencia, $tipoPreferencia, $nombreActividadNatural)
    {
        parent::__construct($idPreferencia, $tipoPreferencia);
        $this->nombreActividadNatural = $nombreActividadNatural;
    }

    public function getNombreActividadNatural()
    {
        return $this->nombreActividadNatural;
    }
}

// Clase hija PreferenciaLiteraria
class PreferenciaLiteraria extends Preferencia
{
    protected $nombreGeneroLiterario;

    public function __construct($idPreferencia, $tipoPreferencia, $nombreGeneroLiterario)
    {
        parent::__construct($idPreferencia, $tipoPreferencia);
        $this->nombreGeneroLiterario = $nombreGeneroLiterario;
    }

    public function getNombreGeneroLiterario()
    {
        return $this->nombreGeneroLiterario;
    }
}

// Clase hija PreferenciaBienestar
class PreferenciaBienestar extends Preferencia
{
    protected $nombrePreferenciaSaludable;

    public function __construct($idPreferencia, $tipoPreferencia, $nombrePreferenciaSaludable)
    {
        parent::__construct($idPreferencia, $tipoPreferencia);
        $this->nombrePreferenciaSaludable = $nombrePreferenciaSaludable;
    }

    public function getNombrePreferenciaSaludable()
    {
        return $this->nombrePreferenciaSaludable;
    }
}
