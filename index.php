<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require 'vendor/autoload.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minerador</title>
    <!-- Link para o CSS do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link para os Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>

        /* Estilo para o overlay de loading */
        .loading-overlay {
            display: none; /* Começa oculto */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fundo escuro com opacidade */
            z-index: 9999; /* Para ficar sobre todo o conteúdo */
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        /* Estilo para o spinner */
        .spinner {
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        /* Animação do spinner */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Estilo para o texto abaixo do spinner */
        .loading-overlay p {
            color: white;
            font-size: 18px;
            margin-top: 20px;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .card-edit{
            height: 100px;
        }

        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        /* .form-container {
            margin-top: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(10, 20, 10, 0.3);
            border: 1px solid #FFD700;
        } */
        .input-group-text {
            background-color: #e9ecef;
        }
        .submit-btn {
            width: 100%;
        }
        .progress-container {
            margin-top: 20px;
        }
        .progress-bar {
            width: 0;
        }
        h3 {
            margin-bottom: 20px;
            font-weight: bold;
        }
        label {
            font-weight: bold;
        }
        #sales-fields {
            display: none; /* Hide the sales fields by default */
        }
    </style>
</head>
<body>

<?php

// Caminho do arquivo
$arquivoClear = '/var/www/html/minerador/dados_em_tempo_real.csv';
file_put_contents($arquivoClear, '');

// Caminho do arquivo
$arquivo = '/var/www/html/minerador/data_received.txt';


// Caminho do arquivo de cancelamento
$cancelFile = 'cancel.txt';
// Cria o arquivo de cancelamento com o valor 1
file_put_contents($cancelFile, '0');

// Verifica se o arquivo existe e, se sim, limpa o conteúdo
if (file_exists($arquivo)) {
    // Abre o arquivo e escreve um conteúdo vazio, limpando o arquivo
    $handle = fopen($arquivo, 'w');
    fclose($handle);
}

// Caminho da pasta onde os arquivos estão
$directory = '/var/www/html/minerador/';

// Verifica se o diretório existe
if (!is_dir($directory)) {
    die("Diretório não encontrado: " . $directory);
}

// Apagar todos os arquivos CSV com o padrão "minerados_*.csv"
$csvFiles = glob($directory . 'minerados_*.csv');
if ($csvFiles === false) {
    // die("Erro ao buscar arquivos CSV.");
} elseif (empty($csvFiles)) {
    // echo "Nenhum arquivo CSV encontrado.<br>";
} else {
    foreach ($csvFiles as $csvFile) {
        if (file_exists($csvFile)) {
            if (unlink($csvFile)) {
                // echo "Arquivo CSV removido: " . $csvFile . "<br>";
            } else {
                // echo "Erro ao remover o arquivo CSV: " . $csvFile . "<br>";
            }
        }
    }
}

// Apagar o arquivo ZIP "minerados_links.zip"
$zipFile = $directory . 'minerados_links.zip';
if (file_exists($zipFile)) {
    if (unlink($zipFile)) {
        // echo "Arquivo ZIP removido: " . $zipFile . "<br>";
    } else {
        // echo "Erro ao remover o arquivo ZIP: " . $zipFile . "<br>";
    }
} else {
    // echo "Arquivo ZIP não encontrado.<br>";
}

?>
 <h4>Conectado: <span id="userCount" class="badge text-bg-success">0</span></h4>
    <div class="container">
        <div class="form-container">
            <h3 class="text-center text-warning">
                <i class="bi bi-boxes text-warning"></i> Maximo Miner
            </h3>
            <form id="meuFormulario" method="POST">
            <div id="input-container">
                <div class="mb-3 input-group">
                    <label for="inputText" class="form-label">Cole o Link</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-download"></i>
                        </span>
                        <input type="text" class="form-control" id="inputText" name="link[]" placeholder="Digite aqui" required>
                        <button class="btn btn-outline-secondary" type="button" id="addButton">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

                <div class="mb-3">
                    <label for="filter" class="form-label">Filtro de Vendas</label>
                    <select class="form-select" id="filter" name="filter" onchange="toggleSalesFields()">
                        <option value="">Selecione o Filtro</option>
                        <option value="sales">Vendas</option>
                    </select>
                </div>

                <!-- Sales Fields (Initially hidden) -->
                <div id="sales-fields" class="row" style="display: none;">
                    <div class="col-6 mb-3">
                        <label for="comparison" class="form-label">Comparação</label>
                        <select class="form-select" id="comparison" name="comparison">
                        <option value="">=</option>    
                        <option value="greater">></option>
                            <option value="less"><</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label for="value" class="form-label">Valor de Vendas</label>
                        <input type="number" class="form-control" id="value" name="value" placeholder="Digite o valor">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="comparison" class="form-label">Internacional</label>
                        <select class="form-select" id="internacional" name="internacional">
                            <option value="no">Não</option>
                            <option value="yes">Sim</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label for="comparison" class="form-label">Paginas</label>
                        <input type="number" class="form-control" id="paginas" name="paginas" max="40" min="1" placeholder="Digite o valor">
                    </div>

                    <div class="mb-3">
                        <label for="showProductsCheckbox" class="form-label">Mostrar Produtos</label>
                        <input type="checkbox" id="showProductsCheckbox">
                    </div>

                </div>

                <button type="submit" class="btn btn-primary submit-btn">Processar</button>
                <div class="baixar"></div>
                  <!-- Botão de cancelar -->
                <button type="button" id="cancelButton" class="btn btn-danger mt-3">Cancelar</button>

                <div id="loading-overlay" class="loading-overlay">
                    <div class="spinner"></div>
                    <p>Aguarde, gerando arquivo...</p>
                </div>

            </form>

            <!-- Barra de Progresso de Percentual -->
            <div class="progress-container">
                <h4>% Percentual</h4>
                <div class="progress">
                    <div id="progressBar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
            </div>

            <!-- Barra de Progresso de Páginas -->
            <div class="progress-container">
            <h4>% Páginas</h4>
            <div class="progress" style="background-color: #ff0000;"> <!-- Cor de fundo vermelha para o restante da barra -->
                <div id="pageProgressBar" class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0/40 páginas</div>
            </div>
        </div>

        <div class="container py-5">
            <!-- Botão para gerar o CSV -->
            <a href="csv.php" target="_blank" class="btn btn-warning">Ver Produtos</a>
        </div>
        </div>
        <span class="badge text-bg-danger">V.1.0</span>
    </div>

    <!-- Aqui os cards de produtos serão inseridos -->
    <div id="products-container"></div>

    <!-- JavaScript do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    
    <!-- Script para mostrar/esconder campos de vendas -->
    <script>

    // Connect to the WebSocket server
    var ws = new WebSocket("ws://localhost:8080");

    ws.onmessage = function(event) {
        // Parse the received data
        var data = JSON.parse(event.data);
        // Update the user count on the webpage
        document.getElementById('userCount').innerText = data.userCount;
    };

    ws.onclose = function() {
        console.log('Connection closed');
    };

    document.getElementById('cancelButton').addEventListener('click', function () {
        if (confirm("Tem certeza de que deseja cancelar o processo?")) {
            // Faz uma requisição para criar o arquivo de cancelamento
            fetch('cancelProcess.php', {
                method: 'POST'
            }).then(response => {
                if (response.ok) {
                    alert("Processo cancelado. O arquivo será gerado com os dados processados até agora.");
                } else {
                    alert("Erro ao tentar cancelar o processo.");
                }
            }).catch(error => {
                console.error('Erro ao cancelar:', error);
            });
        }
    });

    
    function showLoading() {
        document.getElementById('loading-overlay').style.display = 'flex';  // Mostra o overlay
    }

    function hideLoading() {
        document.getElementById('loading-overlay').style.display = 'none';  // Esconde o overlay
    }

    // TIRAR LOADING
    hideLoading();

    function checkFile() {
        fetch('/minerador/checkFile.php')  // Faz a requisição para o PHP
            .then(response => response.json()) // Converte a resposta em JSON
            .then(data => {
                // console.log(data);
                if (data.status === "approved") {
                    pageProgressBar.innerHTML = 'Todas as páginas processadas!';
                         // Verifica se o botão de download já existe
                    if (!document.querySelector('.baixar .download-button')) {
                        pageProgressBar.innerHTML = 'Todas as páginas processadas!';

                        // const downloadButton = document.createElement('a');
                        // downloadButton.href = 'http://localhost/dashboard/minerador/minerados_links.zip';
                        // downloadButton.textContent = 'Baixar Arquivo Zip';
                        // downloadButton.classList.add('btn', 'btn-success', 'col-12', 'btn-block', 'mt-3', 'download-button'); // Adiciona classes de estilo e uma classe única
                        // downloadButton.setAttribute('download', 'minerados_links.zip'); // Garante que o arquivo seja baixado

                        // // Adiciona o botão ao DOM, por exemplo, abaixo da barra de progresso
                        // document.querySelector('.baixar').appendChild(downloadButton);

                        // Tira o loading e para o intervalo
                        hideLoading();
                        clearInterval(intervaloVerificacao); // Para de verificar após encontrar o arquivo
                    }
                }
            })
            .catch(error => console.error('Erro:', error));
    }
    // Verifica o arquivo a cada 1 segundo (1000 milissegundos)
    let intervaloVerificacao = setInterval(checkFile, 1500);
       
    var paginas = document.getElementById('paginas').value = 40;

    document.getElementById('paginas').addEventListener('input', function() {
        const max = 40;
        const min = 1;
        let value = parseInt(this.value);

        if(value > 40){
            this.value = 40;
        }else if(value < 1){
            this.value = 1;
        }

          // Atualiza a mensagem na barra de progresso
        const progressBar = document.getElementById('pageProgressBar');
        progressBar.innerHTML = `0/${value} páginas`;
    });

     document.getElementById('addButton').addEventListener('click', function() {
        // Cria uma nova div com o novo input
        const newInputDiv = document.createElement('div');
        newInputDiv.classList.add('mb-3', 'input-group');

            // Cria o novo input
            newInputDiv.innerHTML = `
                <span class="input-group-text">
                    <i class="bi bi-download"></i>
                </span>
                <input type="text" class="form-control" name="link[]" placeholder="Digite aqui" required>
                <button class="btn btn-outline-secondary removeButton" type="button">
                    <i class="bi bi-dash"></i>
                </button>`;

            // Adiciona o novo input no container
            document.getElementById('input-container').appendChild(newInputDiv);

            // Adiciona funcionalidade para remover o input quando clicar no botão "-"
            const removeButtons = document.querySelectorAll('.removeButton');
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.parentElement.parentElement.remove();
                });
            });
        });

        function toggleSalesFields() {
                const filter = document.getElementById('filter').value;
                const salesFields = document.getElementById('sales-fields');
                
                // Show fields only if "Vendas" is selected
                if (filter === 'sales') {
                    salesFields.style.display = 'flex';  // Show the fields as flex
                } else {
                    salesFields.style.display = 'none';  // Hide the fields
                }
            }

        // Função de progresso (pode manter conforme sua necessidade)
        document.getElementById('meuFormulario').addEventListener('submit', function(event) {
            event.preventDefault();
            checkProgress();
  
            const formData = new FormData(this);
            const progressBar = document.getElementById('progressBar');
            // Obter os valores dos campos adicionais
            const comparison = document.getElementById('comparison').value; // Valor do campo "maior que" ou "menor que"
            const salesValue = document.getElementById('value').value; // Valor do campo "Valor de Vendas"
            const link = document.getElementById('inputText').value; // Valor do campo de URL
            const internacional = document.getElementById('internacional').value;
            // Adicionar os valores ao FormData
            formData.append('comparison', comparison);
            formData.append('sales_value', salesValue);
            formData.append('internacional',internacional);

            let links = [];

            // Coleta todos os valores dos campos de entrada com o name "link[]"
            $('input[name="link[]"]').each(function() {
                let link = $(this).val();
                if (link !== '') {
                    links.push(link);
                }
            });

            // Verifica se há links para enviar
            if (links.length > 0) {
                // Adiciona cada link individualmente ao FormData
                links.forEach((link, index) => {
                    formData.append(`link[${index}]`, link);
                });
            } else {
                alert('Por favor, insira ao menos um link.');
            }

            fetch('/minerador/builder.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            // .then(data => console.log(data));
           });

            function checkProgress() {
            const progressBar = document.getElementById('progressBar');
            const maxPages = document.getElementById('paginas').value;
            let interval = setInterval(() => {
            fetch('/minerador/process.php', {
                    method: 'GET', // Especifica o método GET
                    headers: {
                        'Content-Type': 'application/json' // Define o cabeçalho da requisição
                    }
                })
                .then(response => response.json())
                .then(data => {

                    
                    const links = document.querySelectorAll('input[name="link[]"]'); // Coleta o número de links
                    let totalPages = maxPages * links.length; // Multiplica as páginas pelo número de links


                    const progressPercentage = parseFloat(data.progress).toFixed(2); // Formatar para duas casas decimais
                    progressBar.style.width = progressPercentage + '%';
                    progressBar.innerHTML = progressPercentage + '%';

                    if (progressPercentage >= 100) {
                        progressBar.innerHTML = 'Concluído!';
                    }

                    const currentPage = data.pages; // Número atual de páginas processadas
                    const pageProgress = (currentPage / maxPages) * 100; // Calcula o progresso
                    pageProgressBar.style.width = pageProgress + '%';
                    pageProgressBar.innerHTML = `${currentPage}/${maxPages} páginas`;

                    if (currentPage >= totalPages) {
                        if (progressPercentage >= 100) {
                            //  showLoading();  // Mostra o overlay quando a verificação começa
                             clearInterval(interval);
                        }
                    }
                })
                .catch(error => console.error('Erro ao verificar progresso:', error));
            }, 1500); 
        }
    </script>
</body>
</html>
        