<?php

require_once('vendor/autoload.php');

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Firefox\FirefoxOptions;


set_time_limit(0);
ini_set('memory_limit', '2G'); // Aumenta para 1GB (ou o valor que você precisar)

// Arquivo para salvar o progresso
$progressFile = 'progress.txt';
$progressPage = 'page_count.txt';


// Função para salvar o progresso no arquivo
function updateProgress($percent) {
    global $progressFile;
    file_put_contents($progressFile, $percent);
}

// Função para salvar o progresso no arquivo
function updatePage($page) {
    global $progressPage;
    file_put_contents($progressPage, $page);
}

// Função para obter o número da página atual
function getCurrentPage() {
    global $progressPage;
    if (file_exists($progressPage)) {
        return (int)file_get_contents($progressPage); // Lê o valor salvo no arquivo
    }
    return 1; // Se o arquivo não existir, começamos na página 0
}

// Inicializa a página atual
$currentPage = getCurrentPage(); // Obter a página atual
updatePage(1);
// Atualize o progresso para 0% no início
updateProgress(0);

abstract class Builder{
    protected string $resultado = "";

    abstract function IncluirLinha(array $line);
    abstract function IncluirCabecalho(array $header);
    abstract function finalizar();

    public function getResultado(){
        return $this->resultado;
    }
}

class Token {

    private $token;
    
    public function __construct()
    {
        $url = "https://www.afilidrop.com.br/api/v1/getTokenMl?id=38";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $res = json_decode($response);
        if(curl_errno($ch)) {
            echo 'Erro no cURL: ' . curl_error($ch);
        } else {
            $this->setToken($res->token);
        }
    }

    public function getToken(){
        return $this->token;
    }

    public function setToken($token){
        $this->token = $token;
    }
}


abstract class Diretor {
     
    protected Builder $builder;
    protected FirefoxOptions $options;
    protected $dados;
    protected Token $token;

    public function __construct(Builder $builder)
    {
        $this->options = new FirefoxOptions();
        $this->options->addArguments(['--headless']);  // Ativa o modo headless
        $this->options->addArguments(['--no-sandbox']); // Necessário em alguns sistemas
        $this->options->addArguments(['--disable-dev-shm-usage']); // Necessário 
        $this->options->addArguments(['--disable-gpu']);
        $this->options->addArguments(['--disable-images']); // Desativa o carregamento de imagens
        $this->options->addArguments(['--disable-javascript']); // Desativa o JavaScript (caso não seja necessário)

        // Disfarça o Selenium
        $this->options->addArguments(['--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36']); 
        $this->options->addArguments(['--disable-blink-features=AutomationControlled']); // Remove a flag de automação
        $this->options->addArguments(['--blink-settings=imagesEnabled=false']); // Remove carregamento de imagens
        // Remove mensagens desnecessárias
        $this->options->addArguments(['--silent']); // Executa de forma silenciosa
        $this->builder = $builder;
        $this->dados = new SplFixedArray(1);
        $this->token = new Token();
    }
    public abstract function construir($inputFile);
}


class HtmlMiner extends Builder {

     private $document;
     private $table;

     public function __construct()
     {
        $this->document = new DOMDocument('1.0','UTF-8');
        $this->document->appendChild($this->document->createElement('html'));
        $this->table = $this->document->createElement('table');
        $this->table->setAttribute('border',1);
        $this->document->firstChild->appendChild($this->table);
     }
    

     public function criarTableRow(array $line, $tipo = "td"){
        $tr = $this->document->createElement('tr');
        array_map(fn($v) => 
            $tr->appendChild($this->document->createElement($tipo,$v))
        ,$line);

        $this->table->appendChild($tr);
     }

     function IncluirLinha(array $line){
        $this->criarTableRow($line);
    }

     function IncluirCabecalho(array $header){
        $this->criarTableRow($header,'th');
    }
    
    function finalizar(){
        $this->resultado = $this->document->saveHTML();
    }

    public function getResultado(){
        return $this->resultado;
    }
}


class CsvBuilder extends Builder {

    private $arrayCsv = [];
    private $csvFileHandle;

    function IncluirLinha(array $line){
        $this->arrayCsv[] = $line;
    }

    function IncluirCabecalho(array $header){
        $this->arrayCsv[] = $header;
    }

    function finalizar(){

        foreach ($this->arrayCsv as $line) {
            $this->resultado .= implode(',',$line).PHP_EOL;
        }
    }
}

class CsvBuilderSaveFile extends Builder {

     private $csvFileHandle;

    public function __construct($filename)
    {
        // Abre o arquivo CSV no modo de escrita
        $this->csvFileHandle = fopen($filename, 'w');
        if (!$this->csvFileHandle) {
            throw new Exception("Não foi possível abrir o arquivo CSV.");
        }
    }

    function IncluirLinha(array $line){
        // Escreve uma linha no arquivo CSV em tempo real
        fputcsv($this->csvFileHandle, $line);
        // Força a gravação no disco
        fflush($this->csvFileHandle);
    }

    function IncluirCabecalho(array $header){
        // Escreve o cabeçalho no arquivo CSV
        fputcsv($this->csvFileHandle, $header);
        fflush($this->csvFileHandle); // Garante que os dados sejam gravados no arquivo
    }

    function finalizar(){
        // Fecha o arquivo CSV ao final do processo
        fclose($this->csvFileHandle);
    }
}

class DiretorCsv extends Diretor {

    private $dadosSpecial = [];
    private $nextPage = "";
    private $counter = 0;
    private $cancelFile = 'cancel.txt';
    // Endereço do Selenium Server
    private $serverUrl = 'http://localhost:4444/wd/hub';
    private $totalRequests; // Número total de requisições
    
    public function __construct(Builder $builder, $totalRequests,) {
        parent::__construct($builder);
        $this->totalRequests = $totalRequests;
    }

    public function construir($url,$page = 1,$comparison = null,$sales_value = null,$pg = null,$contLink = 1,$contador = 0) {

        // Configura as capacidades desejadas
        $capabilities = DesiredCapabilities::firefox();
        $capabilities->setCapability(FirefoxOptions::CAPABILITY, $this->options);
        
        // Inicializa o WebDriver
        $driver = RemoteWebDriver::create($this->serverUrl, $capabilities);
        $driver->get($url);

        // Agora você pode capturar o conteúdo da tag <script> normalmente
        $jsonData = $driver->executeScript('return document.getElementById("__PRELOADED_STATE__").textContent;');
        // Decodifica o JSON em um array PHP
        $preloadedState = json_decode($jsonData, true);
        
        $this->nextPage = $preloadedState['pageState']['initialState']['pagination']['next_page']['url'];
        // $this->escreverLog($this->nextPage);
        $produtos = $preloadedState['pageState']['initialState']['melidata_track']['event_data']['results'];

        $multiCurl = [];
        $mh = curl_multi_init();

        $processedLinks = 0;
     
        foreach ($produtos as $link) {

            if ($this->isProcessCancelled($this->cancelFile)) {
                echo "Processo já foi cancelado.";
                break;
            }

            $productPageUrl = "https://api.mercadolibre.com/items/{$link}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $productPageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");      
            curl_multi_add_handle($mh, $ch);
            $multiCurl[$productPageUrl] = $ch;
            sleep(1);
        }
        // Executar as requisições simultâneas
        $running = null;
        $driver->quit();
        do {
            curl_multi_exec($mh, $running);
        } while ($running);
        

        foreach ($multiCurl as $productPageUrl => $ch) {

                // Verifica o cancelamento antes de começar
        if ($this->isProcessCancelled($this->cancelFile)) {
            echo "Processo já foi cancelado.";
            break;
        }

        $content = json_decode(curl_multi_getcontent($ch));

        $this->escreverLog($content->title);
        
        $vendas = 0;
        $estoque = 0;
        try {
            // sleep(1);
            // Configura as capacidades desejadas
            $capabilities = DesiredCapabilities::firefox();
            $capabilities->setCapability(FirefoxOptions::CAPABILITY, $this->options);
            // Inicializa o WebDriver
            $driver = RemoteWebDriver::create($this->serverUrl, $capabilities);
            $driver->get($content->permalink);
            // Aguarde até que a página carregue completamente
            $driver->manage()->timeouts()->implicitlyWait(10);
            // Executa um script JavaScript para capturar o conteúdo da variável window.__PRELOADED_STATE__
            $jsonData = $driver->executeScript('return window.__PRELOADED_STATE__;');

            $Datavendas = $jsonData['initialState']['components']['header']['subtitle'];
            $estoque =    $jsonData['initialState']['components']['available_quantity']['picker']['track']['melidata_event']['event_data']['quantity'];
            $vendedor =   $jsonData['initialState']['components']['available_quantity']['picker']['track']['melidata_event']['event_data']['seller_name'];
            $logo =       $jsonData['initialState']['components']['seller_experiment']['seller_info']['logo'];
            $sellerLink = $jsonData['initialState']['components']['seller_data']['components'][2]['action']['target'];
            $linkCatalogo = $jsonData['initialState']['components']['others_products']['products'][0]["action"]['target'];
            $frase = $jsonData['initialState']['components']['others_products']['products'][0]['title']['text'];

            $driver->quit();
            // Em vez de carregar todo o DOM, busque diretamente o texto desejado com uma expressão regular
            // // A regex ajusta para capturar o número de vendas diretamente
            // if (preg_match('/<span[^>]*class="[^"]*ui-pdp-subtitle[^"]*"[^>]*>(.*?)<\/span>/is', $link, $matches)) {
            //     $salesText = strip_tags($matches[1]); // Remove qualquer tag HTML
        
                $vendas = 0;
                // Ajusta a regex para capturar diferentes formatos de número
                if (preg_match('/\+?(\d+\.?\d*)\s?mil\s?vendidos/i', $Datavendas, $matches)) {
                    // Converte "50mil" para 50000
                    $vendas = intval($matches[1]) * 1000;
                } elseif (preg_match('/\+?(\d+\.?\d*)\s?vendidos/i', $Datavendas, $matches)) {
                    // Captura números sem o "mil" (ex: 300 vendidos)
                    $vendas = intval($matches[1]);
                }
            // } else {
            //     // Caso não encontre o número de vendas, logar ou lidar com o erro
            //     $this->escreverLog("Não foi possível capturar as informações de vendas.");
            // }
        } catch (\Throwable $th) {
            $this->escreverLog($th->getMessage());
        }
        
        // Loop para construir os dados, aplicando o filtro de vendas
        if ($comparison === 'greater') {
            if ($vendas > $sales_value) {
                $data = new DateTime($content->date_created); // Data de Criacao
                $dataAtual = new DateTime();
                $visitas = $this->getItemVisits($content->id);
                $dados = [
                    'nome' => str_replace(',', '', $content->title) != null ? str_replace(',', '',$content->title) : "",
                    'categoria' => $content->category_id,
                    'Preço' => $content->price,
                    'tipo_anuncio' => $this->mapProductType($content->listing_type_id),
                    'visitas' => $visitas,
                    'link' => $content->permalink,
                    'vendas' => $vendas,
                    'data Criação' => $data->format('Y/m/d'),
                    'Dias' => $dataAtual->diff($data)->days,
                    'Internacional' => $content->international_delivery_mode != "none" ? "SIM" : "NÃO",
                    'Catalogo' => $content->catalog_listing == false ? "NÃO" : "SIM",
                    'Saude' => ($content->health * 100)."%",
                    'Vendedor ID' => $content->seller_id,
                    'img' => $content->thumbnail,
                    'conversão' => $this->calcularTaxaConversao($vendas,$visitas),
                    'id' => $content->id,
                    'estoque' => isset($estoque) ? $estoque : "1",
                    'seller' => $vendedor,
                    'logo' => isset($logo) ? $logo : "-",
                    'sellerLink' => $sellerLink,
                    'peopleCatalog' => $frase,
                    'linkCatalog' => $linkCatalogo
                ];

                $this->builder->IncluirLinha($dados);
            }
        } elseif ($comparison === 'less') {
            if ($vendas < $sales_value) {
                $data = new DateTime($content->date_created); // Data de Criacao
                $dataAtual = new DateTime();
                $visitas = $this->getItemVisits($content->id);
                $dados = [
                    'nome' => str_replace(',', '', $content->title) != null ? str_replace(',', '',$content->title) : "",
                    'categoria' => $content->category_id,
                    'Preço' => $content->price,
                    'tipo_anuncio' => $this->mapProductType($content->listing_type_id),
                    'visitas' => $visitas,
                    'link' => $content->permalink,
                    'vendas' => $vendas,
                    'data Criação' => $data->format('Y/m/d'),
                    'Dias' => $dataAtual->diff($data)->days,
                    'Internacional' => $content->international_delivery_mode != "none" ? "SIM" : "NÃO",
                    'Catalogo' => $content->catalog_listing == false ? "NÃO" : "SIM",
                    'Saude' => ($content->health * 100)."%",
                    'Vendedor ID' => $content->seller_id,
                    'img' => $content->thumbnail,
                    'conversão' => $this->calcularTaxaConversao($vendas,$visitas),
                    'id' => $content->id,
                    'estoque' => isset($estoque) ? $estoque : "1",
                    'seller' => $vendedor,
                    'logo' => isset($logo) ? $logo : "-",
                    'sellerLink' => $sellerLink,
                    'peopleCatalog' => $frase,
                    'linkCatalog' => $linkCatalogo
                ];

                $this->builder->IncluirLinha($dados);
            }
        }else{

            $data = new DateTime($content->date_created); // Data de Criacao
            $dataAtual = new DateTime();
            $visitas = $this->getItemVisits($content->id);
            $dados = [
                'nome' => str_replace(',', '', $content->title) != null ? str_replace(',', '',$content->title) : "",
                'categoria' => $content->category_id,
                'Preço' => $content->price,
                'tipo_anuncio' => $this->mapProductType($content->listing_type_id),
                'visitas' => $visitas,
                'link' => $content->permalink,
                'vendas' => $vendas,
                'data Criação' => $data->format('Y/m/d'),
                'Dias' => $dataAtual->diff($data)->days,
                'Internacional' => $content->international_delivery_mode != "none" ? "SIM" : "NÃO",
                'Catalogo' => $content->catalog_listing == false ? "NÃO" : "SIM",
                'Saude' => ($content->health * 100)."%",
                'Vendedor ID' => $content->seller_id,
                'img' => $content->thumbnail,
                'conversao' => $this->calcularTaxaConversao($vendas,$visitas),
                'id' => $content->id,
                'estoque' => isset($estoque) ? $estoque : "1",
                'seller' => $vendedor,
                'logo' => isset($logo) ? $logo : "-",
                'sellerLink' => $sellerLink,
                'peopleCatalog' => $frase,
                'linkCatalog' => $linkCatalogo
            ];
            
            $this->builder->IncluirLinha($dados);
        
        }
            $this->escreverLog("Requisição para: {$productPageUrl} - Contagem atual de requisições: " . ($this->totalRequests + 1));
            $this->totalRequests++; // Incrementa o número total de requisições
            $this->updateTotalRequests($this->totalRequests); // Atualiza o valor no arquivo
     
            // $this->escreverLog(json_encode($this->getItemVisits($content->id)));
            curl_multi_remove_handle($mh, $ch);

            // Atualiza o progresso
            $processedLinks++;
            $percentComplete = ($processedLinks / count($multiCurl)) * 100;
            updateProgress($percentComplete);

               // Inicia a sessão cURL
               $ch = curl_init();
               // Configurações do cURL para enviar os dados via POST
               curl_setopt($ch, CURLOPT_URL,"http://localhost/dashboard/minerador/minerador.php");
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
               curl_setopt($ch, CURLOPT_POST, true);
               curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'pagina' => $this->dados,
                    'progress' => $percentComplete
                ]));
               // Executa a requisição
               $response = curl_exec($ch);
               // Verifica se ocorreu algum erro
               if (curl_errno($ch)) {
                   echo 'Erro: ' . curl_error($ch) . "\n";
               } else {
                    echo json_encode(['status' => 'success']);
               }
        }

        curl_multi_close($mh);


        if($page == $pg){
            $page++;
            $this->counter += 1;
            $this->dados = [];
            if($contLink == $this->counter){
                $this->builder->finalizar();
            }else{
                $this->builder->finalizar();
            }
        }else{
            if(empty($this->nextPage)){
                $this->limparDados($this->dados->toArray()); // filtra o array
                $this->builder->IncluirCabecalho(array_keys($this->dados[0]));
                foreach ($this->dados as $linha) {
                    $this->builder->IncluirLinha($linha);
                }
                $this->builder->finalizar();
            }else{
            $page++; // Incrementa o número da página
            updatePage($page); // Atualiza o
            $this->construir($this->nextPage, $page,$comparison,$sales_value,$pg,$contLink,$this->counter); 
        }
        }

    }
    
    function calcularTaxaConversao($numeroDeConversoes, $numeroDeVisitantes) {
        if ($numeroDeVisitantes == 0) {
            return 0; // Para evitar divisão por zero
        }
        $taxaDeConversao = ($numeroDeConversoes / $numeroDeVisitantes) * 100;
        return $taxaDeConversao;
    }


    function getItemVisits($itemId) {
        sleep(2);
        // IMPLEMENTAR NO SERVIDOR
        $url = "https://api.mercadolibre.com/visits/items?ids=" . $itemId;
        // Inicializa cURL
        $ch = curl_init();
        // Configurações cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$this->token->getToken()
        ));
        // Executa a requisição
        $response = curl_exec($ch);
        // Verifica se ocorreu erro
        if (curl_errno($ch)) {
            echo 'Erro no cURL: ' . curl_error($ch);
            curl_close($ch);
            return 0;
        }
        // Fecha a conexão cURL
        curl_close($ch);
        // Decodifica a resposta JSON
        $data = json_decode($response, true);
        // Retorna os dados
        return array_values($data)[0];
    }

    // Função para salvar o número total de requisições no arquivo
    function updateTotalRequests($count) {
        global $totalRequestsFile;
        file_put_contents($totalRequestsFile, $count);
    }
      /**
     * Set the value of dados
     */
    public function setDados($dados): self
    {
        $this->dados = $dados;

        return $this;
    }

    function getHttpContent($url, $driver) {
                // Abra a URL fornecida
        $driver->get($url);
        // Aguarde até que a página carregue completamente
        $driver->manage()->timeouts()->implicitlyWait(10);
        // Executa um script JavaScript para capturar o conteúdo da variável window.__PRELOADED_STATE__
        $jsonData = $driver->executeScript('return window.__PRELOADED_STATE__;');
        return $jsonData;
    }
    
    private function mapProductType($productType) {
        // Cria um array de mapeamento
        $mapping = [
            'gold_pro' => 'classico',
            'gold_special' => 'premium'
        ];
    
        // Verifica se o tipo de produto existe no mapeamento
        if (array_key_exists($productType, $mapping)) {
            return $mapping[$productType];
        }
    
        // Retorna null ou um valor padrão se o tipo de produto não for encontrado
        return null;  // ou você pode retornar uma string como 'desconhecido'
    }

    // Função para limpar os dados, removendo campos com valores vazios, nulos ou "-"
    private function limparDados(array $dados) {
        return array_filter($dados, function($valor) {
            return !empty($valor) && $valor !== "-";
        });
    }

    // Função para verificar o cancelamento
    public function isProcessCancelled($cancelFile) {
        // Se o arquivo de cancelamento não existir, cria-o com o valor '0'
        if (!file_exists($cancelFile)) {
            file_put_contents($cancelFile, '0'); // Valor inicial: 0 (não cancelado)
        }

        // Verifica o conteúdo do arquivo cancel.txt
        $cancelStatus = file_get_contents($cancelFile);
        return trim($cancelStatus) === '1'; // Somente cancela se o valor for '1'
    }


    // Função para gravar o log
    public function escreverLog($mensagem) {
    $arquivoLog = 'log.txt'; // Caminho do arquivo de log
    $dataHora = date('Y-m-d H:i:s'); // Data e hora atuais
    $logMensagem = "[" . $dataHora . "] -" . $mensagem . "\n"; // Formato da mensagem

    // Escreve a mensagem no arquivo
    file_put_contents($arquivoLog, $logMensagem, FILE_APPEND);
}
}

$totalRequestsFile = 'total_requests.txt'; // Arquivo para salvar o número total de requisições
$totalRequests = 0; // Inicializando a variável
// Uso do CsvBuilderSaveFile no seu fluxo
$csvFilename = 'dados_em_tempo_real.csv';
$builder = new CsvBuilderSaveFile($csvFilename);
$director = new DiretorCsv($builder, $totalRequests);
$director->construir($_REQUEST['link'][0], 1, $_REQUEST['comparison'], $_REQUEST['value'], $_REQUEST['paginas'], count($_REQUEST['link']));
