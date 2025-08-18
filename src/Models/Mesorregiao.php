<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

class Mesorregiao
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly Estado $estado
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            nome: $data['nome'],
            estado: Estado::fromArray($data['UF'])
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'estado' => $this->estado->toArray()
        ];
    }
}