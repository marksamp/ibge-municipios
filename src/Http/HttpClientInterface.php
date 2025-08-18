<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Http;

use Marksamp\IbgeLocalidades\Exceptions\HttpException;

interface HttpClientInterface
{
    /**
     * Executa uma requisição HTTP GET
     *
     * @param string $url
     * @param array<string, string> $headers
     * @return array<mixed>
     * @throws HttpException
     */
    public function get(string $url, array $headers = []): array;
}