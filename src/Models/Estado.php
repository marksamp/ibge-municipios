<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

class Estado
{
    public function __construct(
        public readonly int $id,
        public readonly string $sigla,
        public readonly string $nome,
        public readonly Regiao $regiao
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            sigla: $data['sigla'],
            nome: $data['nome'],
            regiao: Regiao::fromArray($data['regiao'])
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