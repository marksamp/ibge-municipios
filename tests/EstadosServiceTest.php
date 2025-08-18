<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Tests;

use PHPUnit\Framework\TestCase;
use Marksamp\IbgeLocalidades\Services\EstadosService;
use Marksamp\IbgeLocalidades\Http\HttpClientInterface;
use Marksamp\IbgeLocalidades\Models\Estado;
use Marksamp\IbgeLocalidades\Models\Regiao;
use Marksamp\IbgeLocalidades\Exceptions\IbgeApiException;

class EstadosServiceTest extends TestCase
{
    private function createMockHttpClient(array $response): HttpClientInterface
    {
        $mock = $this->createMock(HttpClientInterface::class);
        $mock->method('get')->willReturn($response);
        return $mock;
    }

    public function testTodosRetornaArrayDeEstados(): void
    {
        $mockResponse = [
            [
                'id' => 35,
                'sigla' => 'SP',
                'nome' => 'São Paulo',
                'regiao' => [
                    'id' => 3,
                    'sigla' => 'SE',
                    'nome' => 'Sudeste'
                ]
            ]
        ];

        $httpClient = $this->createMockHttpClient($mockResponse);
        $service = new EstadosService($httpClient);

        $estados = $service->todos();

        $this->assertIsArray($estados);
        $this->assertCount(1, $estados);
        $this->assertInstanceOf(Estado::class, $estados[0]);
        $this->assertEquals('São Paulo', $estados[0]->nome);
        $this->assertEquals('SP', $estados[0]->sigla);
    }

    public function testPorIdRetornaEstado(): void
    {
        $mockResponse = [
            'id' => 35,
            'sigla' => 'SP',
            'nome' => 'São Paulo',
            'regiao' => [
                'id' => 3,
                'sigla' => 'SE',
                'nome' => 'Sudeste'
            ]
        ];

        $httpClient = $this->createMockHttpClient($mockResponse);
        $service = new EstadosService($httpClient);

        $estado = $service->porId(35);

        $this->assertInstanceOf(Estado::class, $estado);
        $this->assertEquals(35, $estado->id);
        $this->assertEquals('São Paulo', $estado->nome);
    }

    public function testPorSiglaInexistenteThrowsException(): void
    {
        $mockResponse = [
            [
                'id' => 35,
                'sigla' => 'SP',
                'nome' => 'São Paulo',
                'regiao' => [
                    'id' => 3,
                    'sigla' => 'SE',
                    'nome' => 'Sudeste'
                ]
            ]
        ];

        $httpClient = $this->createMockHttpClient($mockResponse);
        $service = new EstadosService($httpClient);

        $this->expectException(IbgeApiException::class);
        $this->expectExceptionMessage("Estado com sigla 'XX' não encontrado");

        $service->porSigla('XX');
    }
}