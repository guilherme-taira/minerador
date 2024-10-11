<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Carregar o CSS do Bootstrap e DataTables -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.bootstrap4.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

    <!-- DataTables Buttons JS para exportação -->
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.1/css/fixedHeader.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/fixedheader/3.2.1/js/dataTables.fixedHeader.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    

      /* Customização adicional */
      table.dataTable thead {
        background-color: #007bff;
        color: white;
    }
    table.dataTable tbody tr {
        background-color: #f9f9f9;
    }
    .table-responsive {
        margin-top: 20px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background-color: #007bff;
        color: white !important;
    }
    .dataTables_wrapper .dataTables_length select {
        width: auto;
        display: inline-block;
    }
    /* OUTROS DADOS */
        img {
            width: auto;
            height: auto;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .filter-group {
            margin-top: 20px;
        }
        .badge {
            font-size: 0.85rem;
            padding: 0.4em 0.6em;
        }
        body {
            padding: 20px;
        }


/* CSS */
.button-5 {
  align-items: center;
  background-clip: padding-box;
  background-color: #fa6400;
  border: 1px solid transparent;
  border-radius: .25rem;
  box-shadow: rgba(0, 0, 0, 0.02) 0 1px 3px 0;
  box-sizing: border-box;
  color: #fff;
  cursor: pointer;
  display: inline-flex;
  font-family: system-ui,-apple-system,system-ui,"Helvetica Neue",Helvetica,Arial,sans-serif;
  font-size: 16px;
  font-weight: 600;
  justify-content: center;
  line-height: 1.25;
  margin: 0;
  min-height: 3rem;
  padding: calc(.875rem - 1px) calc(1.5rem - 1px);
  position: relative;
  text-decoration: none;
  transition: all 250ms;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: baseline;
  width: auto;
}

.button-5:hover,
.button-5:focus {
  background-color: #fb8332;
  box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
}

.button-5:hover {
  transform: translateY(-1px);
}

.button-5:active {
  background-color: #c85000;
  box-shadow: rgba(0, 0, 0, .06) 0 2px 4px;
  transform: translateY(0);
}

.logo{
    width: 32px;
    height: 32px;
}


table.dataTable td, table.dataTable th {
    padding: 4px; /* Reduz o padding para 4px */
    font-size: 14px; /* Reduzir o tamanho da fonte */
}

/* Imagens menores */
table.dataTable img {
    max-width: 80px; /* Reduz o tamanho máximo da imagem */
    height: auto;
}

/* Botões menores */
table.dataTable .btn, .button-5 {
   
    font-size: 12px; /* Reduz o tamanho da fonte dos botões */
}

/* Altura mínima da linha */
table.dataTable tbody tr {
    height: 40px; /* Define a altura mínima das linhas */
}

/* Reduzir o tamanho do badge */
.badge {
    font-size: 12px; /* Diminuir a fonte do badge */
    padding: 2px 6px; /* Reduz o padding do badge */
}

    </style>
</head>
<body>


    <!-- Modal -->
    <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Adicione modal-lg ou modal-xl aqui -->
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalDetalhesLabel">Detalhes do Produto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="spinner-border text-success" role="status"><span class="visually-hidden"></span></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>    
    </div>


    <div class="row filter-group">
        <!-- Coluna esquerda: Filtro -->
        <div class="col-md-6">
            <label for="vendasFiltro">Filtrar por vendas:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <select id="filtroTipo" class="custom-select">
                        <option value="greater">Maior que</option>
                        <option value="less">Menor que</option>
                    </select>
                </div>
                <input type="number" id="vendasInput" class="form-control" placeholder="Número de vendas" min="0" value="0">
                <div class="input-group-append">
                    <button id="aplicarFiltro" class="btn btn-primary">Aplicar Filtro</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-md-3">
        <label for="filtroCatalogo">Filtrar por Catálogo:</label>
        <select id="filtroCatalogo" class="form-control">
            <option value="">Todos</option>
            <option value="SIM">Sim</option>
            <option value="NÃO">Não</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="filtroDias">Filtrar por Dias < </label>
        <input type="number" id="filtroDias" class="form-control" placeholder="Número de dias">
    </div>
</div>

    <!-- Tabela Responsiva -->
    <div class="table-responsive">
            <table id="csvTable" class="table table-bordered dt-responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Seller</th>
                    <th>Estoque</th>
                    <th>Preço</th>
                    <th>Tipo Anúncio</th>
                    <th>Visitas</th>
                    <th>Conversão</th>
                    <th>Metricas</th>
                    <th>Vendas</th>
                    <th>Data Criação</th>
                    <th>Dias</th>
                    <th>Internacional</th>
                    <th>Catálogo</th>
                    <th>Pessoas Anúnciando catalogo: </th>
                    <th>Saúde</th>
                    <th>ID do Vendedor</th>
                    <th>ID Produto</th>
                </tr>
            </thead>
            <tbody id="csvBody">
                <!-- Dados do CSV serão adicionados aqui -->
            </tbody>
        </table>
    </div>

<!-- JavaScript do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


<script>

$(document).ready(function() {
    let myChart = null;  // Inicialize a variável com null
    let dataTable = $('#csvTable').DataTable({
         responsive: true,
         fixedHeader: true, // Ativa o cabeçalho fixo
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/Portuguese-Brasil.json"
        },
        "pageLength": 100, // Número padrão de resultados por página (20)
        "lengthMenu": [ [10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"] ], // Opções para seleção de itens por página
        "dom": 'Bfrtip', // Define a posição dos botões
        "buttons": [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar para Excel',
                    className: 'btn btn-success',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 5) {
                                    // Exporta o link completo
                                    return $(data).attr('href');
                                } else if (column === 0) {
                                    // Exporta a URL da imagem ou deixa vazio
                                    return $(data).find('img').attr('src') || '';
                                } else {
                                    // Remove tags HTML
                                    return data.replace(/<\/?[^>]+(>|$)/g, "");
                                }
                            }
                        }
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: 'Exportar para CSV',
                    className: 'btn btn-info',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 5) {
                                    return $(data).attr('href');
                                } else if (column === 0) {
                                    return $(data).find('img').attr('src') || '';
                                } else {
                                    return data.replace(/<\/?[^>]+(>|$)/g, "");
                                }
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Exportar para PDF',
                    className: 'btn btn-danger',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 5) {
                                    return $(data).attr('href');
                                } else if (column === 0) {
                                    return ''; // Opcionalmente, pode deixar a URL da imagem
                                } else {
                                    return data.replace(/<\/?[^>]+(>|$)/g, "");
                                }
                            }
                        }
                    }
                }
            ]
    });

      let autoUpdate; // Variável que armazenará o intervalo
    
        // Função que formata o conteúdo ao expandir a linha (personalize conforme necessário)
    function format(d) {
        return 'Detalhes do produto: ' + d[1]; 
    }

     // Função de filtro personalizada para "Vendas", "Catálogo" e "Dias"
     $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        let filtroTipo = $('#filtroTipo').val();
        let vendasInput = parseInt($('#vendasInput').val(), 10);
        let vendas = parseInt(data[9].replace(/[^0-9]/g, '')) || 0; // Coluna de Vendas

        let catalogoFiltro = $('#filtroCatalogo').val();
        let catalogo = data[13].trim(); // Coluna de Catálogo

        let diasFiltro = parseInt($('#filtroDias').val(), 10);
        let dias = parseInt(data[11].replace(/[^0-9]/g, '')) || 0; // Coluna de Dias

        // Filtrar por vendas
        if ((isNaN(vendasInput) || vendasInput === 0 || (filtroTipo === "greater" && vendas > vendasInput) || (filtroTipo === "less" && vendas < vendasInput)) &&
            (catalogoFiltro === "" || catalogo === catalogoFiltro) &&
            (isNaN(diasFiltro) || diasFiltro === 0 || dias <= diasFiltro)) { // Alteração feita aqui para <=
            return true;
        }
        return false;
    });


    // Aplica o filtro ao clicar no botão
    $('#aplicarFiltro').on('click', function() {
        dataTable.draw();
    });

    function extrairNumero(frase) {
         // Usando regex para extrair o primeiro número encontrado
        const match = frase.match(/\d+/);
        
        // Retorna o número ou null se não houver correspondência
        return match ? parseInt(match[0], 10) : null;
    }

    function atualizarTabelaCSV() {
        $.ajax({
            url: 'rota_get_csv.php', // Rota que retorna o CSV em formato JSON
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                let tableData = [];

                if (data.length > 0) {
            
                    // Adiciona as linhas do CSV
                    for (let i = 1; i < data.length; i++) {
                     
                        let row = data[i];
                        let tableRow = [];
                        // Trunca o nome se for maior que 50 caracteres
                        let nome = row[0];
                        if (nome.length > 0) {
                            nome = nome.substring(0, 40) + '...';
                        }

                        // Adiciona a imagem como primeira coluna
                        let imageUrl = row[13]; // A URL da imagem está na última coluna do CSV
                        tableRow.push('<img src="' + imageUrl + '" alt="Imagem">');
                        // Adiciona os outros dados nas colunas subsequentes
                        tableRow.push('<a href="' + row[5] + '" target="_blank">'+nome+'</a>');
                        tableRow.push('<a href="' + row[19] + '" target="_blank">'+'<img class="logo" src="' + row[18] + '">'+row[17].substring(0, 10) + '...' + '</a>');  // Seller
                        tableRow.push(row[16]);  // Estoque
                        tableRow.push(row[2]);  // Preço
                        tableRow.push(row[3]);  // Tipo de Anúncio
                        tableRow.push(`<strong> <i class="bi bi-eye"></i> ${row[4]} </strong>`);
                        tableRow.push(`<strong> ${parseFloat(row[14]).toFixed(2)}% </strong>`);
                        // tableRow.push('<a href="' + row[4] + '" target="_blank">Link</a>');  // Link (abre em uma nova aba)
                        tableRow.push('<a href="#" class="button-5" data-bs-toggle="modal" data-bs-target="#modalDetalhes" data-item-id="' + row[15] + '"><i class="bi bi-graph-up-arrow"></i></a>');  // Inclui o itemId no data-item-id
                        // Link que abre o modal

                        // Adiciona a coluna de vendas com a badge de sucesso
                        let vendas = parseInt(row[6]) || 0;
                        let badge = vendas > 0 ? `<span class="badge badge-success">${vendas} Vendas</span>` : `<span class="badge badge-danger">${vendas} Venda</span>`; // Badge para vendas > 20
                        tableRow.push(badge);  // Vendas com badge "Success"

                        tableRow.push(row[7]);  // Data de Criação
                        tableRow.push(row[8]);  // Dias
                        let Internacional = row[9] == "SIM" ? `<span class="badge badge-info">${row[9]}</span>` : `<span class="badge badge-info">${row[9]}</span>`; // Badge para vendas > 20
                        tableRow.push(Internacional);  // Internacional
                        let catalogo = row[10] == "SIM" ? `<span class="badge badge-success">${row[10]}</span>` : `<span class="badge badge-danger">${row[10]}</span>`; // Badge para vendas > 20
                        if(row[10] == "SIM"){
                            tableRow.push(catalogo);  // Catálogo
                            tableRow.push(
                                ` <span class="position-absolute top-0 start-100 translate-middle">
                                    +${extrairNumero(row[20]) != null ? extrairNumero(row[20]) : "Único"}
                                </span>`
                            );  // Catálogo
                        }else{
                            tableRow.push(catalogo);  // Catálogo
                            tableRow.push( 0);  // 
                        }
                      
                        tableRow.push(row[11]); // Saúde
                        tableRow.push(row[12]); // ID do Vendedor
                        tableRow.push(row[15]); // ID do Produto
                        tableData.push(tableRow);
                    }

                    // Limpa e adiciona novas linhas ao DataTables, mantendo o estado da tabela
                    dataTable.clear().rows.add(tableData).draw(false); // O "false" mantém a página e a ordenação atuais
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro na requisição AJAX: ', status, error);
            }
        });
    }


     // Função para controlar a expansão dos detalhes (clicando no "+")
     $('#csvTable tbody').on('click', 'tr td:first-child', function() {
        let tr = $(this).closest('tr');
        let row = dataTable.row(tr);

        if (row.child.isShown()) {
            startAutoUpdate();  // Retoma a atualização automática ao fechar o detalhe
        } else {
            stopAutoUpdate();   // Pausa a atualização automática ao expandir o detalhe
        }
    });

     // Função para atualizar a tabela automaticamente
     function startAutoUpdate() {
        autoUpdate = setInterval(atualizarTabelaCSV, 2000); // Atualiza a cada 2 segundos
    }

    // Função para parar a atualização automática
    function stopAutoUpdate() {
        clearInterval(autoUpdate); // Para o intervalo de atualização
    }

    // Inicializar a atualização automática
    startAutoUpdate();

     // Função para obter a data atual no formato YYYY-MM-DD
     function getCurrentDate() {
        let today = new Date();
        let year = today.getFullYear();
        let month = ('0' + (today.getMonth() + 1)).slice(-2); // Adiciona 0 se for necessário
        let day = ('0' + today.getDate()).slice(-2); // Adiciona 0 se for necessário
        return `${year}-${month}-${day}`;
    }

 // Função para fazer a requisição para o endpoint da API Mercado Libre
function getVisits(itemId,accessToken) {
    let currentDate = getCurrentDate();  // Pega a data atual
    $('#modalDetalhes .modal-body').html(`<div class="spinner-border text-success" role="status"><span class="visually-hidden"></span></div>`);
            
    var url = `https://afilidrop.com.br/api/v1/getVisits?item=${itemId}&currentDate=${currentDate}&access_token=${accessToken}`;
    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            console.log(url);
            $('#modalDetalhes .modal-body').html(`<canvas id="myChart" width="800" height="400"></canvas>`);
            
            let data;
            try {
                // Converte a resposta para JSON, caso seja uma string
                data = typeof response === "string" ? JSON.parse(response) : response;
            } catch (error) {
                console.error("Erro ao parsear o JSON:", error.message);
                return;  // Encerra o processamento em caso de erro
            }

            // Verificar se a propriedade results existe e é um array
            if (data.results && Array.isArray(data.results)) {
                const labels = data.results.map(result => new Date(result.date).toLocaleDateString());  // Extrai as datas
                const dataPoints = data.results.map(result => result.total);  // Extrai o total de visitas

                  // Se o gráfico já existe, destrua-o antes de criar um novo
                  if (myChart) {
                    myChart.destroy();
                 }

                // Configurar o gráfico de linha
                const ctx = document.getElementById('myChart').getContext('2d');
                 myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,  // Datas
                        datasets: [{
                            label: 'Visitas',
                            data: dataPoints,  // Totais de visitas
                            borderColor: 'rgba(255, 140, 0, 1)',  // Cor da linha laranja forte
                            backgroundColor: 'rgba(255, 140, 0, 0.6)',  // Cor de preenchimento laranja forte com opacidade maior
                            fill: true,
                            tension: 0.3  // Suavizar a linha
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: (ctx) => 'Visitas Totais : ' + data.total_visits,
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Data'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Visitas'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        
        },
        error: function(xhr, status, error) {
            $('#modalDetalhes .modal-body').html(`<p>Erro ao carregar os dados de visitas: ${xhr.responseText}</p>`);
        }
    });
}


      // Função para obter o token
      function getTokenAndVisits(itemId) {
        $.ajax({
            url: 'https://www.afilidrop.com.br/api/v1/getTokenMl?id=38',  // Endpoint para obter o token
            type: 'GET',
            success: function(response) {
                let accessToken = response.token;  // Supondo que o token esteja na propriedade "token"
                // Chama a função getVisits com o token e o itemId
                getVisits(itemId, accessToken);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao obter o token:', status, error);
                $('#modalDetalhes .modal-body').html(`<p>Erro ao obter o token de acesso.</p>`);
            }
        });
    }

   // Exemplo de uso da função ao clicar no link
   $('#csvBody').on('click', 'a[data-bs-target="#modalDetalhes"]', function() {
        let itemId = $(this).data('item-id');  // Pega o itemId do atributo data-item-id
        console.log(itemId);
        getTokenAndVisits(itemId);  // Chama a função que obtém o token e depois busca as visitas
    });
    
});
</script>

</body>
</html>
