<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

final class Regiao
{
    public function __construct(
        public readonly int $id,
        public readonly string $sigla,
        public readonly string $nome
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            sigla: (string) $data['sigla'],
            nome: (string) $data['nome']
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