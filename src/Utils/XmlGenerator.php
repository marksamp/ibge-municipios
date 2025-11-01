<?php

declare(strict_types=1);

namespace Marksamp\IbgeLocalidades\Utils;

use Marksamp\IbgeLocalidades\IbgeClient;
use Marksamp\IbgeLocalidades\Models\Estado;
use Marksamp\IbgeLocalidades\Models\Municipio;
use Marksamp\IbgeLocalidades\Exceptions\IbgeApiException;
use DOMDocument;
use DOMElement;

class XmlGenerator
{
    private IbgeClient $client;

    /**
     * Mapeamento das capitais brasileiras por UF
     * @var array<string, string>
     */
    private array $capitais = [
        'AC' => 'Rio Branco',
        'AL' => 'Maceió',
        'AP' => 'Macapá',
        'AM' => 'Manaus',
        'BA' => 'Salvador',
        'CE' => 'Fortaleza',
        'DF' => 'Brasília',
        'ES' => 'Vitória',
        'GO' => 'Goiânia',
        'MA' => 'São Luís',
        'MT' => 'Cuiabá',
        'MS' => 'Campo Grande',
        'MG' => 'Belo Horizonte',
        'PA' => 'Belém',
        'PB' => 'João Pessoa',
        'PR' => 'Curitiba',
        'PE' => 'Recife',
        'PI' => 'Teresina',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Natal',
        'RS' => 'Porto Alegre',
        'RO' => 'Porto Velho',
        'RR' => 'Boa Vista',
        'SC' => 'Florianópolis',
        'SP' => 'São Paulo',
        'SE' => 'Aracaju',
        'TO' => 'Palmas'
    ];

    public function __construct(?IbgeClient $client = null)
    {
        $this->client = $client ?? IbgeClient::create();
    }

    /**
     * Verifica se um município é capital
     */
    private function isCapital(string $nomeMunicipio, string $siglaEstado): bool
    {
        if (!isset($this->capitais[$siglaEstado])) {
            return false;
        }

        return $nomeMunicipio === $this->capitais[$siglaEstado];
    }

    /**
     * Gera XML com todos os estados e municípios
     *
     * @param bool $formatado Se true, gera XML formatado/identado
     * @return string XML gerado
     * @throws IbgeApiException
     */
    public function gerarXmlCompleto(bool $formatado = true): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = $formatado;

        // Elemento raiz
        $root = $dom->createElement('brasil');
        $dom->appendChild($root);

        // Buscar todos os estados
        echo "Buscando estados...\n";
        $estados = $this->client->estados()->todos();

        $totalEstados = count($estados);
        $estadoAtual = 0;

        foreach ($estados as $estado) {
            $estadoAtual++;
            echo "Processando [{$estadoAtual}/{$totalEstados}] {$estado->nome}...\n";

            // Criar elemento estado simplificado
            $estadoElement = $dom->createElement('estado');
            $estadoElement->appendChild($dom->createElement('id', (string)$estado->id));
            $estadoElement->appendChild($dom->createElement('sigla', $estado->sigla));
            $estadoElement->appendChild($dom->createElement('nome', $estado->nome));

            // Buscar municípios do estado
            $municipios = $this->client->municipios()->porEstado($estado->sigla);

            // Tag municipios com quantidade
            $municipiosElement = $dom->createElement('municipios');
            $municipiosElement->setAttribute('qtd', (string)count($municipios));

            foreach ($municipios as $municipio) {
                $municipioElement = $dom->createElement('municipio', $municipio->nome);
                $municipioElement->setAttribute('id', (string)$municipio->id);

                // Adicionar atributo capital apenas se for capital
                if ($this->isCapital($municipio->nome, $estado->sigla)) {
                    $municipioElement->setAttribute('capital', '1');
                }

                $municipiosElement->appendChild($municipioElement);
            }

            $estadoElement->appendChild($municipiosElement);
            $root->appendChild($estadoElement);
        }

        echo "XML gerado com sucesso!\n";

        $xml = $dom->saveXML();
        return $xml !== false ? $xml : '';
    }

    /**
     * Gera XML apenas com as capitais
     */
    public function gerarXmlCapitais(bool $formatado = true): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = $formatado;

        $root = $dom->createElement('capitais');
        $root->setAttribute('qtd', (string)count($this->capitais));
        $dom->appendChild($root);

        echo "Buscando capitais...\n";
        $estados = $this->client->estados()->todos();

        foreach ($estados as $estado) {
            echo "Processando capital de {$estado->nome}...\n";

            $municipios = $this->client->municipios()->porEstado($estado->sigla);

            foreach ($municipios as $municipio) {
                if ($this->isCapital($municipio->nome, $estado->sigla)) {
                    $capitalElement = $dom->createElement('capital', $municipio->nome);
                    $capitalElement->setAttribute('id', (string)$municipio->id);
                    $capitalElement->setAttribute('uf', $estado->sigla);

                    $root->appendChild($capitalElement);
                    break;
                }
            }
        }

        $xml = $dom->saveXML();
        return $xml !== false ? $xml : '';
    }

    /**
     * Gera XML de um estado específico com seus municípios
     */
    public function gerarXmlEstado(string $siglaEstado, bool $formatado = true): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = $formatado;

        echo "Buscando informações do estado {$siglaEstado}...\n";

        $estado = $this->client->estados()->porSigla($siglaEstado);

        // Criar elemento estado simplificado
        $root = $dom->createElement('estado');
        $root->appendChild($dom->createElement('id', (string)$estado->id));
        $root->appendChild($dom->createElement('sigla', $estado->sigla));
        $root->appendChild($dom->createElement('nome', $estado->nome));
        $dom->appendChild($root);

        echo "Buscando municípios...\n";
        $municipios = $this->client->municipios()->porEstado($siglaEstado);

        // Tag municipios com quantidade
        $municipiosElement = $dom->createElement('municipios');
        $municipiosElement->setAttribute('qtd', (string)count($municipios));

        foreach ($municipios as $municipio) {
            $municipioElement = $dom->createElement('municipio', $municipio->nome);
            $municipioElement->setAttribute('id', (string)$municipio->id);

            // Adicionar atributo capital apenas se for capital
            if ($this->isCapital($municipio->nome, $siglaEstado)) {
                $municipioElement->setAttribute('capital', '1');
            }

            $municipiosElement->appendChild($municipioElement);
        }

        $root->appendChild($municipiosElement);

        $xml = $dom->saveXML();
        return $xml !== false ? $xml : '';
    }

    /**
     * Salva XML em arquivo
     */
    public function salvarArquivo(string $xml, string $nomeArquivo): bool
    {
        $resultado = file_put_contents($nomeArquivo, $xml);

        if ($resultado === false) {
            return false;
        }

        echo "Arquivo salvo: {$nomeArquivo}\n";
        echo "Tamanho: " . number_format($resultado / 1024, 2) . " KB\n";

        return true;
    }
}