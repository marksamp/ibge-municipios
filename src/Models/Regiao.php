<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

class Regiao
{
    public int $id;
    public string $sigla;
    public string $nome;

    public function __construct(int $id, string $sigla, string $nome)
    {
        $this->id = $id;
        $this->sigla = $sigla;
        $this->nome = $nome;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            (string) $data['sigla'],
            (string) $data['nome']
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
            'nome' => $this->nome
        ];
    }
}