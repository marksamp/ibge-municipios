# IBGE Localidades PHP Library

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Uma biblioteca PHP moderna e extensível para consumir as APIs do IBGE, com foco inicial na API de localidades (Estados e Municípios).

## Características

- ✅ **PHP 8.0.7+**: Compatível com versões estáveis do PHP 8
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

- PHP 8.0.7 ou superior
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
// Buscar todos os estados (ordem alfabética por padrão)
$estados = $ibge->estados()->todos();

// Buscar estado por ID
$estado = $ibge->estados()->porId(35);

// Buscar estado por sigla
$estado = $ibge->estados()->porSigla('SP');

// Buscar estados por região (ordem alfabética)
$estados = $ibge->estados()->porRegiao(3); // Sudeste

// Ordenações alternativas
$estados = $ibge->estados()->todosOrdemOriginal(); // Ordem da API
$estados = $ibge->estados()->todosPorId();         // Por ID crescente
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
// Buscar todos os municípios (ordem alfabética - cuidado: são muitos!)
$municipios = $ibge->municipios()->todos();

// Buscar município por ID
$municipio = $ibge->municipios()->porId(3550308);

// Buscar municípios por estado (ordem alfabética)
$municipios = $ibge->municipios()->porEstado('SP');
$municipios = $ibge->municipios()->porEstado(35);

// Buscar municípios por microrregião (ordem alfabética)
$municipios = $ibge->municipios()->porMicrorregiao(11);

// Buscar municípios por mesorregião (ordem alfabética)
$municipios = $ibge->municipios()->porMesorregiao(15);

// Buscar municípios por nome (ordem alfabética)
$municipios = $ibge->municipios()->buscarPorNome('Santos');

// Ordenações alternativas
$municipios = $ibge->municipios()->todosOrdemOriginal(); // Ordem da API
$municipios = $ibge->municipios()->todosPorId();         // Por ID crescente
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

## Ordenação dos Resultados

Por padrão, todos os métodos que retornam arrays (lista de estados ou municípios) ordenam os resultados **alfabeticamente por nome**. Isso facilita a apresentação em interfaces de usuário.

### Ordenação Padrão (Alfabética)
```php
$estados = $ibge->estados()->todos(); // A-Z por nome
$municipios = $ibge->municipios()->porEstado('SP'); // A-Z por nome
```

### Ordenações Alternativas
```php
// Estados
$estados = $ibge->estados()->todosOrdemOriginal(); // Ordem da API do IBGE
$estados = $ibge->estados()->todosPorId();         // Por ID (1, 11, 12...)

// Municípios  
$municipios = $ibge->municipios()->todosOrdemOriginal(); // Ordem da API
$municipios = $ibge->municipios()->todosPorId();         // Por ID crescente
```

## Utilitários

### Gerador de XML

A biblioteca inclui um utilitário para gerar arquivos XML com estados e municípios brasileiros.

#### Recursos do Gerador

- ✅ XML com todos os estados e municípios
- ✅ XML apenas com as capitais brasileiras
- ✅ XML de um estado específico
- ✅ Destaque automático para capitais
- ✅ Formatação e identação
- ✅ Metadados incluídos

#### Uso Básico

```php
use Marksamp\IbgeLocalidades\Utils\XmlGenerator;

$generator = new XmlGenerator();

// Gerar XML com todas as capitais
$xml = $generator->gerarXmlCapitais();
$generator->salvarArquivo($xml, 'capitais.xml');

// Gerar XML de um estado específico
$xml = $generator->gerarXmlEstado('SP');
$generator->salvarArquivo($xml, 'sao_paulo.xml');

// Gerar XML completo (todos estados e municípios)
$xml = $generator->gerarXmlCompleto();
$generator->salvarArquivo($xml, 'brasil_completo.xml');
```

#### Scripts Prontos

```bash
# Modo interativo com menu de opções
php gerar_xml.php

# Modo automático (gera apenas capitais)
php gerar_xml_automatico.php
```

#### Estrutura do XML Gerado

**XML de Capitais:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<capitais qtd="27">
  <capital id="3550308" uf="SP">São Paulo</capital>
  <capital id="3304557" uf="RJ">Rio de Janeiro</capital>
  <capital id="2304400" uf="CE">Fortaleza</capital>
  <!-- ... mais 24 capitais ... -->
</capitais>
```

**XML de Estado Completo:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<estado>
  <id>35</id>
  <sigla>SP</sigla>
  <nome>São Paulo</nome>
  <municipios qtd="645">
    <municipio id="3500105">Adamantina</municipio>
    <municipio id="3500204">Adolfo</municipio>
    <!-- ... municípios em ordem alfabética ... -->
    <municipio id="3550308" capital="1">São Paulo</municipio>
    <!-- ... mais municípios ... -->
  </municipios>
</estado>
```

**XML Completo (Brasil):**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<brasil>
  <estado>
    <id>12</id>
    <sigla>AC</sigla>
    <nome>Acre</nome>
    <municipios qtd="22">
      <municipio id="1200013">Acrelândia</municipio>
      <municipio id="1200054">Assis Brasil</municipio>
      <!-- ... municípios em ordem alfabética ... -->
      <municipio id="1200401" capital="1">Rio Branco</municipio>
      <!-- ... mais municípios ... -->
    </municipios>
  </estado>
  <!-- ... todos os 27 estados ... -->
</brasil>
```

**Características do XML:**
- ✅ Estrutura simplificada e limpa
- ✅ Estado: apenas `id`, `sigla` e `nome` como tags
- ✅ Atributo `qtd` na tag `municipios`
- ✅ Municípios sem agrupamento de regiões
- ✅ Municípios em ordem alfabética
- ✅ Município: apenas `id` como atributo e nome como conteúdo
- ✅ Capital identificada com `capital="1"` (não aparece se não for capital)

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

### v1.2.0
- ✅ **XML SIMPLIFICADO**: Estrutura otimizada e mais limpa
- ✅ Estado: informações como tags (`<id>`, `<sigla>`, `<nome>`)
- ✅ Atributo `qtd` em vez de `total` para quantidade
- ✅ Capital: apenas `capital="1"` (não aparece em não-capitais)
- ✅ Removido agrupamento de micro/mesorregião
- ✅ Removidos metadados desnecessários
- ✅ Tamanho dos arquivos XML reduzido em ~50%

### v1.1.0
- ✅ Adicionado utilitário gerador de XML
- ✅ Suporte para gerar XML completo (estados + municípios)
- ✅ Suporte para gerar XML apenas com capitais
- ✅ Suporte para gerar XML de estado específico
- ✅ Destaque automático para capitais no XML
- ✅ Scripts prontos (interativo e automático)
- ✅ Ordenação alfabética de resultados por padrão
- ✅ Métodos alternativos de ordenação
- ✅ Conversão completa para PHP 8.0.7

### v1.0.0
- Implementação inicial
- Suporte para Estados e Municípios
- Arquitetura extensível
- Documentação completa