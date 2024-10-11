<?php
// Exemplo básico de como pegar os produtos sem eval()

// Verifica se o arquivo existe
$filename = 'data_received.txt';

if (!file_exists($filename)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Arquivo de produtos não encontrado.'
    ]);
    exit;
}

// Lê o conteúdo do arquivo
$fileContent = file_get_contents($filename);
$data = json_decode($fileContent);


// Verifica se a decodificação foi bem-sucedida
if (!$fileContent) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao decodificar os dados.'
        ]);
    exit;
}

// Retorna os dados em JSON
echo json_encode([
    'status' => 'success',
    'products' => $data->pagina // Acessa o array de produtos
]);
?>
