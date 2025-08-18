<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Http;

use Marksamp\IbgeLocalidades\Exceptions\HttpException;
use CurlHandle;

class CurlHttpClient implements HttpClientInterface
{
    private int $timeout;
    private int $connectTimeout;

    public function __construct(int $timeout = 30, int $connectTimeout = 10)
    {
        $this->timeout = $timeout;
        $this->connectTimeout = $connectTimeout;
    }

    public function get(string $url, array $headers = []): array
    {
        $curl = curl_init();

        if (!$curl instanceof CurlHandle) {
            throw new HttpException('Falha ao inicializar cURL');
        }

        $defaultHeaders = [
            'Accept: application/json',
            'User-Agent: marksamp/ibge-localidades PHP Client'
        ];

        $formattedHeaders = array_merge($defaultHeaders, $this->formatHeaders($headers));

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_HTTPHEADER => $formattedHeaders,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'marksamp/ibge-localidades PHP Client'
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);

        curl_close($curl);

        if ($response === false || !empty($error)) {
            throw new HttpException("Erro na requisição cURL: {$error}");
        }

        if ($httpCode !== 200) {
            throw new HttpException("HTTP {$httpCode}: Falha na requisição para {$url}");
        }

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpException('Erro ao decodificar resposta JSON: ' . json_last_error_msg());
        }

        return $decodedResponse ?? [];
    }

    /**
     * @param array<string, string> $headers
     * @return array<string>
     */
    private function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }
        return $formatted;
    }
}