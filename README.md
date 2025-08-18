# IBGE Localidades PHP Library

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Uma biblioteca PHP moderna e extensível para consumir as APIs do IBGE, com foco inicial na API de localidades (Estados e Municípios).

## Características

- ✅ **PHP 8+**: Utiliza recursos modernos do PHP como tipos de união, propriedades readonly e match expressions
- ✅ **PSR-4**: Autoloading compatível com padrões PSR
- ✅ **Arquitetura extensível**: Facilmente extensível para outras APIs do IBGE
- ✅ **Tratamento de erros**: Exceptions específicas para diferentes tipos de erro
- ✅ **Type Safety**: Tipagem forte em todos os métodos e propriedades
- ✅ **Zero dependências externas**: Usa apenas extensões nativas do PHP
- ✅ **Modelos ricos**: Classes de modelo com métodos utilitários

## Instalação

### Via Composer

```bash
composer require marksamp/ibge-localidades
```

### Requisitos

- PHP 8.0 ou superior
- Extensão cURL
- Extensão JSON

## Uso Básico

```php
<?php

require_once 'vendor/autoload.php';

use Marksamp\IbgeLocalidades\IbgeClient;

// Criar instância do cliente
$ibge = IbgeClient::create();

// Buscar todos os estados
$estados = $ibge->estados()->todos();

// Buscar estado por sigla
$sp = $ibge->estados()->porSigla('SP');

// Buscar municípios de um estado
$municipiosSP = $ibge->municipios()->porEstado('SP');
```

## API de Estados

### Métodos Disponíveis

```php
// Buscar todos os estados
$estados = $ibge->estados()->todos();

// Buscar estado por ID
$estado = $ibge->estados()->porId(35);

// Buscar estado por sigla
$estado = $ibge->estados()->porSigla('SP');

// Buscar estados por região
$estados = $ibge->estados()->porRegiao(3); // Sudeste
```

### Exemplo com Estados

```php
$sp = $ibge->estados()->porSigla('SP');

echo $sp->nome;           // "São Paulo"
echo $sp->sigla;          // "SP"
echo $sp->id;             // 35
echo $sp->regiao->nome;   // "Sudeste"
echo $sp->regiao->sigla;  // "SE"

// Converter para array
$dados = $sp->toArray();
```

## API de Municípios

### Métodos Disponíveis

```php
// Buscar todos os municípios (cuidado: são muitos!)
$municipios = $ibge->municipios()->todos();

// Buscar município por ID
$municipio = $ibge->municipios()->porId(3550308);

// Buscar municípios por estado (ID ou sigla)
$municipios = $ibge->municipios()->porEstado('SP');
$municipios = $ibge->municipios()->porEstado(35);

// Buscar municípios por microrregião
$municipios = $ibge->municipios()->porMicrorregiao(11);

// Buscar municípios por mesorregião
$municipios = $ibge->municipios()->porMesorregiao(15);

// Buscar municípios por nome (busca parcial)
$municipios = $ibge->municipios()->buscarPorNome('Santos');
```

### Exemplo com Municípios

```php
$saoPaulo = $ibge->municipios()->porId(3550308);

echo $saoPaulo->nome; // "São Paulo"
echo $saoPaulo->id;   // 3550308

if ($saoPaulo->microrregiao) {
    echo $saoPaulo->microrregiao->nome;
    
    if ($saoPaulo->microrregiao->mesorregiao) {
        echo $saoPaulo->microrregiao->mesorregiao->nome;
        echo $saoPaulo->microrregiao->mesorregiao->estado->nome;
    }
}
```

## Tratamento de Erros

A biblioteca fornece uma hierarquia de exceptions bem definida:

```php
use Marksamp\IbgeLocalidades\Exceptions\IbgeLocalidadesException; // Exception base
use Marksamp\IbgeLocalidades\Exceptions\IbgeApiException;          // Erros da API
use Marksamp\IbgeLocalidades\Exceptions\HttpException;             // Erros HTTP

try {
    $estado = $ibge->estados()->porSigla('XX');
} catch (IbgeApiException $e) {
    echo "Erro na API do IBGE: " . $e->getMessage();
} catch (HttpException $e) {
    echo "Erro HTTP: " . $e->getMessage();
} catch (IbgeLocalidadesException $e) {
    echo "Erro geral da biblioteca: " . $e->getMessage();
}
```

### Hierarquia de Exceptions

```
Exception (PHP nativa)
└── IbgeLocalidadesException (Exception base da biblioteca)
    ├── HttpException (Erros de comunicação HTTP)
    └── IbgeApiException (Erros específicos da API do IBGE)
```

## Estrutura dos Modelos

### Estado

```php
class Estado {
    public readonly int $id;
    public readonly string $sigla;
    public readonly string $nome;
    public readonly Regiao $regiao;
}
```

### Município

```php
class Municipio {
    public readonly int $id;
    public readonly string $nome;
    public readonly ?Microrregiao $microrregiao;
    public readonly ?string $regiaoImediata;
}
```

### Região

```php
class Regiao {
    public readonly int $id;
    public readonly string $sigla;
    public readonly string $nome;
}
```

## Customização

### Cliente HTTP Personalizado

Você pode injetar seu próprio cliente HTTP implementando a interface `HttpClientInterface`:

```php
use Marksamp\IbgeLocalidades\Http\HttpClientInterface;

class MeuHttpClient implements HttpClientInterface 
{
    public function get(string $url, array $headers = []): array
    {
        // Sua implementação personalizada
    }
}

$ibge = new IbgeClient(new MeuHttpClient());
```

## Extensibilidade

A biblioteca foi projetada para ser facilmente extensível. Para adicionar suporte a outras APIs do IBGE:

1. Crie novos modelos na pasta `src/Models/`
2. Crie novos serviços na pasta `src/Services/` estendendo `BaseService`
3. Adicione os serviços ao `IbgeClient`

Exemplo de extensão para API de CEP:

```php
class CepService extends BaseService 
{
    public function buscar(string $cep): Endereco
    {
        $data = $this->makeRequest("/localidades/cep/{$cep}");
        return Endereco::fromArray($data);
    }
}
```

## Exemplos Avançados

### Busca de Municípios por População

```php
// Buscar todos os municípios de SP e filtrar por nome
$municipiosSP = $ibge->municipios()->porEstado('SP');

// Filtrar municípios que começam com 'São'
$municipiosSao = array_filter($municipiosSP, function($municipio) {
    return str_starts_with($municipio->nome, 'São');
});

// Ordenar por nome
usort($municipiosSao, fn($a, $b) => $a->nome <=> $b->nome);
```

### Cache de Resultados

```php
class CachedIbgeClient 
{
    private array $cache = [];
    
    public function __construct(private IbgeClient $client) {}
    
    public function getEstados(): array 
    {
        if (!isset($this->cache['estados'])) {
            $this->cache['estados'] = $this->client->estados()->todos();
        }
        
        return $this->cache['estados'];
    }
}
```

## Contribuição

Contribuições são bem-vindas! Por favor:

1. Faça um fork do projeto
2. Crie uma branch para sua feature
3. Faça commit das suas mudanças
4. Faça push para a branch
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para detalhes.

## APIs do IBGE Suportadas

### Atual
- ✅ Localidades (Estados e Municípios)

### Planejadas
- 🔄 CEP
- 🔄 Distritos
- 🔄 Subdistritos
- 🔄 Regiões

## Links Úteis

- [API de Localidades do IBGE](https://servicodados.ibge.gov.br/api/docs/localidades)
- [Documentação oficial do IBGE](https://servicodados.ibge.gov.br/api/docs)

## Changelog

### v1.0.0
- Implementação inicial
- Suporte para Estados e Municípios
- Arquitetura extensível
- Documentação completa