<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

class Microrregiao
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly ?Mesorregiao $mesorregiao = null
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            nome: $data['nome'],
            mesorregiao: isset($data['mesorregiao']) ? Mesorregiao::fromArray($data['mesorregiao']) : null
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
            'mesorregiao' => $this->mesorregiao?->toArray()
        ];
    }
}