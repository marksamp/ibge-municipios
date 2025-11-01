# Utilitário Gerador de XML - Estados e Municípios

Este utilitário permite gerar arquivos XML estruturados com informações sobre estados e municípios brasileiros, obtidos diretamente da API do IBGE.

## 🎯 Características

- ✅ **XML Completo**: Gera arquivo com todos os 27 estados e 5.570 municípios
- ✅ **XML de Capitais**: Lista apenas as 27 capitais brasileiras
- ✅ **XML por Estado**: Gera arquivo individual para qualquer estado
- ✅ **Destaque para Capitais**: Capitais recebem atributos especiais no XML
- ✅ **Formatação**: XML identado e legível
- ✅ **Metadados**: Inclui data de geração e fonte dos dados
- ✅ **Ordem Alfabética**: Municípios listados em ordem alfabética

## 🚀 Como Usar

### Opção 1: Script Interativo

```bash
php gerar_xml.php
```

Menu de opções:
1. Gerar XML completo (todos os estados e municípios)
2. Gerar XML apenas com as capitais ⭐ **Recomendado para começar**
3. Gerar XML de um estado específico
4. Gerar todos os XMLs

### Opção 2: Script Automático

Gera apenas o XML das capitais sem interação:

```bash
php gerar_xml_automatico.php
```

### Opção 3: Programático

```php
<?php
require_once 'vendor/autoload.php';

use Marksamp\IbgeLocalidades\Utils\XmlGenerator;

$generator = new XmlGenerator();

// Gerar XML das capitais
$xml = $generator->gerarXmlCapitais(true);
$generator->salvarArquivo($xml, 'capitais.xml');

// Gerar XML de São Paulo
$xml = $generator->gerarXmlEstado('SP', true);
$generator->salvarArquivo($xml, 'sao_paulo.xml');

// Gerar XML completo (CUIDADO: pode demorar!)
$xml = $generator->gerarXmlCompleto(true);
$generator->salvarArquivo($xml, 'brasil.xml');
```

## 📁 Arquivos Gerados

Os arquivos XML são salvos no diretório `xml_output/`:

```
xml_output/
├── capitais_brasileiras.xml    (~3 KB)
├── estado_SP.xml                (~150 KB)
├── estado_RJ.xml                (~80 KB)
└── brasil_completo.xml          (~15 MB)
```

## 📋 Estrutura do XML

### XML de Capitais (SIMPLIFICADO)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<capitais qtd="27">
  <capital id="3550308" uf="SP">São Paulo</capital>
  <capital id="3304557" uf="RJ">Rio de Janeiro</capital>
  <capital id="2304400" uf="CE">Fortaleza</capital>
  <capital id="3106200" uf="MG">Belo Horizonte</capital>
  <!-- ... mais 23 capitais ... -->
</capitais>
```

**Características:**
- Tag raiz `<capitais>` com atributo `qtd` (quantidade total)
- Cada capital em uma tag `<capital>` com:
    - Atributo `id`: código IBGE do município
    - Atributo `uf`: sigla do estado
    - Conteúdo: nome da capital

### XML de Estado (SIMPLIFICADO)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<estado>
  <id>35</id>
  <sigla>SP</sigla>
  <nome>São Paulo</nome>
  <municipios qtd="645">
    <municipio id="3500105">Adamantina</municipio>
    <municipio id="3500204">Adolfo</municipio>
    <municipio id="3500303">Aguaí</municipio>
    <!-- ... municípios em ordem alfabética ... -->
    <municipio id="3550308" capital="1">São Paulo</municipio>
    <!-- ... mais municípios ... -->
  </municipios>
</estado>
```

**Características:**
- Tags do estado: `<id>`, `<sigla>`, `<nome>`
- Tag `<municipios>` com atributo `qtd` (quantidade de municípios)
- Cada município em tag `<municipio>` com:
    - Atributo `id`: código IBGE do município
    - Atributo `capital="1"`: **apenas se for capital** (não aparece em municípios comuns)
    - Conteúdo: nome do município
- Municípios listados em **ordem alfabética**
- **SEM agrupamento** de micro/mesorregião

### XML Completo (SIMPLIFICADO)

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
      <municipio id="1200104">Brasiléia</municipio>
      <!-- ... municípios em ordem alfabética ... -->
      <municipio id="1200401" capital="1">Rio Branco</municipio>
      <!-- ... mais municípios ... -->
    </municipios>
  </estado>
  
  <estado>
    <id>27</id>
    <sigla>AL</sigla>
    <nome>Alagoas</nome>
    <municipios qtd="102">
      <municipio id="2700102">Água Branca</municipio>
      <!-- ... municípios ... -->
      <municipio id="2704302" capital="1">Maceió</municipio>
      <!-- ... mais municípios ... -->
    </municipios>
  </estado>
  
  <!-- ... todos os 27 estados ... -->
</brasil>
```

**Características:**
- Estrutura muito mais limpa e enxuta
- Apenas informações essenciais
- Fácil de processar e parsear
- Menor tamanho de arquivo

## 🔍 Atributos Especiais

### Para Capitais

Municípios que são capitais recebem atributos adicionais:

```xml
<municipio id="2304400" capital="true" destaque="CAPITAL DO ESTADO">
  <nome>Fortaleza</nome>
  ...
</municipio>
```

- `capital="true"`: Identifica que é uma capital
- `destaque="CAPITAL DO ESTADO"`: Texto descritivo

### IDs no XML

Todos os elementos principais têm atributo `id` com o código oficial do IBGE:

- Estados: ID do estado (ex: 35 = São Paulo)
- Municípios: Código completo de 7 dígitos (ex: 3550308 = São Paulo/SP)
- Microrregiões e Mesorregiões: IDs oficiais do IBGE

## ⚠️ Considerações Importantes

### Tempo de Processamento

| Tipo | Tempo Aproximado | Tamanho |
|------|------------------|---------|
| Capitais | 30-60 segundos | ~3 KB |
| Um Estado | 10-20 segundos | ~50-200 KB |
| Brasil Completo | 10-20 minutos | ~15 MB |

### Consumo de API

- O XML completo faz **28 requisições** à API do IBGE (1 para listar estados + 27 para cada estado)
- Recomenda-se não executar múltiplas vezes seguidas
- Use cache local dos XMLs gerados

### Capitais Incluídas

O utilitário reconhece automaticamente as 27 capitais:

- **Norte**: Manaus (AM), Belém (PA), Macapá (AP), Boa Vista (RR), Rio Branco (AC), Porto Velho (RO), Palmas (TO)
- **Nordeste**: São Luís (MA), Teresina (PI), Fortaleza (CE), Natal (RN), João Pessoa (PB), Recife (PE), Maceió (AL), Aracaju (SE), Salvador (BA)
- **Centro-Oeste**: Brasília (DF), Goiânia (GO), Cuiabá (MT), Campo Grande (MS)
- **Sudeste**: São Paulo (SP), Rio de Janeiro (RJ), Belo Horizonte (MG), Vitória (ES)
- **Sul**: Curitiba (PR), Florianópolis (SC), Porto Alegre (RS)

## 💡 Casos de Uso

### 1. Sistemas de Cadastro
Use o XML de capitais para popular selects de cidades principais.

### 2. Análise Geográfica
XML completo para análise de distribuição regional.

### 3. APIs e Webservices
Sirva os XMLs gerados como fonte de dados offline.

### 4. Integração com Outros Sistemas
Importe os XMLs em bancos de dados, planilhas, etc.

### 5. Backup de Dados
Mantenha uma cópia local atualizada das localidades.

## 🛠️ Personalização

### Alterar Formatação

```php
// XML sem formatação (compacto)
$xml = $generator->gerarXmlCapitais(false);

// XML formatado (padrão)
$xml = $generator->gerarXmlCapitais(true);
```

### Adicionar Mais Capitais ou Dados

Edite o arquivo `src/Utils/XmlGenerator.php` e modifique o array `$capitais` se necessário.

## 🐛 Troubleshooting

### Erro: "Cannot create directory"
```bash
chmod 755 xml_output
```

### XML muito grande
Gere XMLs individuais por estado em vez do completo.

### Timeout
Aumente o `max_execution_time` no php.ini ou no script:
```php
set_time_limit(1800); // 30 minutos
```

### Erro de memória
Aumente `memory_limit`:
```php
ini_set('memory_limit', '512M');
```

## 📊 Estatísticas

- **27 Estados** brasileiros
- **5.570 Municípios** (aproximadamente)
- **27 Capitais**
- **5 Regiões**: Norte, Nordeste, Centro-Oeste, Sudeste, Sul

## 📝 Licença

Este utilitário faz parte da biblioteca `marksamp/ibge-localidades` e está sob licença MIT.