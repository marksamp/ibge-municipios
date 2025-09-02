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

        $estados = array_map(function($item) {
            return Estado::fromArray($item);
        }, $data);

        // Ordenar por nome alfabeticamente
        usort($estados, function($a, $b) {
            return $a->nome <=> $b->nome;
        });

        return $estados;
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

        $estados = array_map(function($item) {
            return Estado::fromArray($item);
        }, $data);

        // Ordenar por nome alfabeticamente
        usort($estados, function($a, $b) {
            return $a->nome <=> $b->nome;
        });

        return $estados;
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

    /**
     * Retorna todos os estados sem ordenação (ordem original da API)
     *
     * @return array<Estado>
     * @throws IbgeApiException
     */
    public function todosOrdemOriginal(): array
    {
        $data = $this->makeRequest('/localidades/estados');

        $estados = array_map(function($item) {
            return Estado::fromArray($item);
        }, $data);

        return $estados;
    }

    /**
     * Retorna todos os estados ordenados por ID
     *
     * @return array<Estado>
     * @throws IbgeApiException
     */
    public function todosPorId(): array
    {
        $data = $this->makeRequest('/localidades/estados');

        $estados = array_map(function($item) {
            return Estado::fromArray($item);
        }, $data);

        // Ordenar por ID
        usort($estados, function($a, $b) {
            return $a->id <=> $b->id;
        });

        return $estados;
    }
}