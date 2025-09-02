<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

class Microrregiao
{
    public int $id;
    public string $nome;
    public ?Mesorregiao $mesorregiao;

    public function __construct(int $id, string $nome, ?Mesorregiao $mesorregiao = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->mesorregiao = $mesorregiao;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['nome'],
            isset($data['mesorregiao']) ? Mesorregiao::fromArray($data['mesorregiao']) : null
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
            'mesorregiao' => $this->mesorregiao !== null ? $this->mesorregiao->toArray() : null
        ];
    }
}