<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Services;

use Marksamp\IbgeLocalidades\Http\HttpClientInterface;
use Marksamp\IbgeLocalidades\Exceptions\IbgeApiException;

abstract class BaseService
{
    protected const BASE_URL = 'https://servicodados.ibge.gov.br/api/v1';
    protected HttpClientInterface $httpClient;
    public function __construct(HttpClientInterface $httpClient) {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $endpoint
     * @return array<mixed>
     * @throws IbgeApiException
     */
    protected function makeRequest(string $endpoint): array
    {
        $url = self::BASE_URL . $endpoint;

        try {
            return $this->httpClient->get($url);
        } catch (\Exception $e) {
            throw new IbgeApiException(
                "Erro ao consultar API do IBGE: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }
}