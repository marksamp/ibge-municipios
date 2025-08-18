<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Services;

use Marksamp\IbgeLocalidades\Models\Municipio;
use Marksamp\IbgeLocalidades\Exceptions\IbgeApiException;

class MunicipiosService extends BaseService
{
    /**
     * Retorna todos os municípios do Brasil
     *
     * @return array<Municipio>
     * @throws IbgeApiException
     */
    public function todos(): array
    {
        $data = $this->makeRequest('/localidades/municipios');

        $municipios = array_map(fn($item) => Municipio::fromArray($item), $data);

        // Ordenar por nome alfabeticamente
        usort($municipios, fn($a, $b) => $a->nome <=> $b->nome);

        return $municipios;
    }

    /**
     * Busca um município por ID
     *
     * @param int $id
     * @return Municipio
     * @throws IbgeApiException
     */
    public function porId(int $id): Municipio
    {
        $data = $this->makeRequest("/localidades/municipios/{$id}");

        return Municipio::fromArray($data);
    }

    /**
     * Busca municípios por estado
     *
     * @param int|string $estado ID ou sigla do estado
     * @return array<Municipio>
     * @throws IbgeApiException
     */
    public function porEstado(int|string $estado): array
    {
        $data = $this->makeRequest("/localidades/estados/{$estado}/municipios");

        $municipios = array_map(fn($item) => Municipio::fromArray($item), $data);

        // Ordenar por nome alfabeticamente
        usort($municipios, fn($a, $b) => $a->nome <=> $b->nome);

        return $municipios;
    }

    /**
     * Busca municípios por microrregião
     *
     * @param int $microrregiaoId
     * @return array<Municipio>
     * @throws IbgeApiException
     */
    public function porMicrorregiao(int $microrregiaoId): array
    {
        $data = $this->makeRequest("/localidades/microrregioes/{$microrregiaoId}/municipios");

        $municipios = array_map(fn($item) => Municipio::fromArray($item), $data);

        // Ordenar por nome alfabeticamente
        usort($municipios, fn($a, $b) => $a->nome <=> $b->nome);

        return $municipios;
    }

    /**
     * Busca municípios por mesorregião
     *
     * @param int $mesorregiaoId
     * @return array<Municipio>
     * @throws IbgeApiException
     */
    public function porMesorregiao(int $mesorregiaoId): array
    {
        $data = $this->makeRequest("/localidades/mesorregioes/{$mesorregiaoId}/municipios");

        $municipios = array_map(fn($item) => Municipio::fromArray($item), $data);

        // Ordenar por nome alfabeticamente
        usort($municipios, fn($a, $b) => $a->nome <=> $b->nome);

        return $municipios;
    }

    /**
     * Busca municípios por nome (busca parcial)
     *
     * @param string $nome
     * @return array<Municipio>
     * @throws IbgeApiException
     */
    public function buscarPorNome(string $nome): array
    {
        $todos = $this->todos();
        $nome = strtolower($nome);

        $municipios = array_filter($todos, fn($municipio) =>
        str_contains(strtolower($municipio->nome), $nome)
        );

        // Converter para array indexado e manter ordem alfabética (já ordenado em todos())
        return array_values($municipios);
    }

    /**
     * Retorna todos os municípios sem ordenação (ordem original da API)
     *
     * @return array<Municipio>
     * @throws IbgeApiException
     */
    public function todosOrdemOriginal(): array
    {
        $data = $this->makeRequest('/localidades/municipios');

        return array_map(fn($item) => Municipio::fromArray($item), $data);
    }

    /**
     * Retorna todos os municípios ordenados por ID
     *
     * @return array<Municipio>
     * @throws IbgeApiException
     */
    public function todosPorId(): array
    {
        $data = $this->makeRequest('/localidades/municipios');

        $municipios = array_map(fn($item) => Municipio::fromArray($item), $data);

        // Ordenar por ID
        usort($municipios, fn($a, $b) => $a->id <=> $b->id);

        return $municipios;
    }
}