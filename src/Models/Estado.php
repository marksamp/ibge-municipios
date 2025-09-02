<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

class Estado
{
    public int $id;
    public string $sigla;
    public string $nome;
    public Regiao $regiao;

    public function __construct(int $id, string $sigla, string $nome, Regiao $regiao)
    {
        $this->id = $id;
        $this->sigla = $sigla;
        $this->nome = $nome;
        $this->regiao = $regiao;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['sigla'],
            $data['nome'],
            Regiao::fromArray($data['regiao'])
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sigla' => $this->sigla,
            'nome' => $this->nome,
            'regiao' => $this->regiao->toArray()
        ];
    }
}