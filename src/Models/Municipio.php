<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

class Municipio
{
    public int $id;
    public string $nome;
    public ?Microrregiao $microrregiao;
    public ?string $regiaoImediata;

    public function __construct(int $id, string $nome, ?Microrregiao $microrregiao = null, ?string $regiaoImediata = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->microrregiao = $microrregiao;
        $this->regiaoImediata = $regiaoImediata;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['nome'],
            isset($data['microrregiao']) ? Microrregiao::fromArray($data['microrregiao']) : null,
            $data['regiao-imediata']['nome'] ?? null
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