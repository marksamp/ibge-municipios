<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

class Municipio
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly ?Microrregiao $microrregiao = null,
        public readonly ?string $regiaoImediata = null
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            nome: $data['nome'],
            microrregiao: isset($data['microrregiao']) ? Microrregiao::fromArray($data['microrregiao']) : null,
            regiaoImediata: $data['regiao-imediata']['nome'] ?? null
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
            'microrregiao' => $this->microrregiao?->toArray(),
            'regiao_imediata' => $this->regiaoImediata
        ];
    }
}