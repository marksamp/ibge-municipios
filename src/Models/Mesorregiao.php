<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Models;

class Mesorregiao
{
    public int $id;
    public string $nome;
    public Estado $estado;

    public function __construct(int $id, string $nome, Estado $estado)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->estado = $estado;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['nome'],
            Estado::fromArray($data['UF'])
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