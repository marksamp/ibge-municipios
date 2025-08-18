<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Services;

use Marksamp\IbgeLocalidades\Models\Estado;
use Marksamp\IbgeLocalidades\Exceptions\IbgeApiException;

class EstadosService extends BaseService
{
    /**
     * Retorna todos os estados do Brasil
     *
     * @return array<Estado>
     * @throws IbgeApiException
     */
    public function todos(): array
    {
        $data = $this->makeRequest('/localidades/estados');

        return array_map(fn($item) => Estado::fromArray($item), $data);
    }

    /**
     * Busca um estado por ID
     *
     * @param int $id
     * @return Estado
     * @throws IbgeApiException
     */
    public function porId(int $id): Estado
    {
        $data = $this->makeRequest("/localidades/estados/{$id}");

        return Estado::fromArray($data);
    }

    /**
     * Busca estados por região
     *
     * @param int $regiaoId
     * @return array<Estado>
     * @throws IbgeApiException
     */
    public function porRegiao(int $regiaoId): array
    {
        $data = $this->makeRequest("/localidades/regioes/{$regiaoId}/estados");

        return array_map(fn($item) => Estado::fromArray($item), $data);
    }

    /**
     * Busca estado por sigla
     *
     * @param string $sigla
     * @return Estado
     * @throws IbgeApiException
     */
    public function porSigla(string $sigla): Estado
    {
        $sigla = strtoupper($sigla);
        $estados = $this->todos();

        foreach ($estados as $estado) {
            if ($estado->sigla === $sigla) {
                return $estado;
            }
        }

        throw new IbgeApiException("Estado com sigla '{$sigla}' não encontrado");
    }
}