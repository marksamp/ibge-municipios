<?php

declare(strict_types=1);

// Arquivo de bootstrap para evitar problemas de carregamento
// Este arquivo garante que as classes sejam carregadas na ordem correta

// Carregar exceptions primeiro
require_once __DIR__ . '/Exceptions/IbgeLocalidadesException.php';
require_once __DIR__ . '/Exceptions/HttpException.php';
require_once __DIR__ . '/Exceptions/IbgeApiException.php';

// Carregar interfaces
require_once __DIR__ . '/Http/HttpClientInterface.php';

// Carregar models na ordem correta (evitando dependências circulares)
require_once __DIR__ . '/Models/Regiao.php';
require_once __DIR__ . '/Models/Estado.php';
require_once __DIR__ . '/Models/Mesorregiao.php';
require_once __DIR__ . '/Models/Microrregiao.php';
require_once __DIR__ . '/Models/Municipio.php';