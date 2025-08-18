<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades;

use Marksamp\IbgeLocalidades\Http\HttpClientInterface;
use Marksamp\IbgeLocalidades\Http\CurlHttpClient;
use Marksamp\IbgeLocalidades\Services\EstadosService;
use Marksamp\IbgeLocalidades\Services\MunicipiosService;

class IbgeClient
{
    private EstadosService $estadosService;
    private MunicipiosService $municipiosService;

    public function __construct(?HttpClientInterface $httpClient = null)
    {
        $httpClient = $httpClient ?? new CurlHttpClient();

        $this->estadosService = new EstadosService($httpClient);
        $this->municipiosService = new MunicipiosService($httpClient);
    }

    public function estados(): EstadosService
    {
        return $this->estadosService;
    }

    public function municipios(): MunicipiosService
    {
        return $this->municipiosService;
    }

    /**
     * Cria uma nova instância do cliente
     */
    public static function create(?HttpClientInterface $httpClient = null): self
    {
        return new self($httpClient);
    }
}