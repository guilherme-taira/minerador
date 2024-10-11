<?php
// Defina o cabeçalho da API para aceitar JSON
// header('Content-Type: application/json');

// Função para salvar os dados recebidos em um arquivo txt, sobrescrevendo-o
function saveDataToFile($data) {
    $filename = 'data_received.txt';

    // Prepara o conteúdo para salvar (em formato legível)
    // Escreve os dados no arquivo, sobrescrevendo o conteúdo anterior
    file_put_contents($filename, print_r(json_encode($data), true));
}

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados enviados no corpo da requisição
    $receivedData = json_decode(file_get_contents('php://input'), true);

    // Verifica se os dados foram enviados corretamente
    if (!empty($receivedData)) {
        // Chama a função para salvar os dados
        saveDataToFile($receivedData);

        // Responde com sucesso
        echo json_encode(['status' => 'success', 'message' => 'Data received and saved successfully']);
    } else {
        // Responde com erro se os dados não forem válidos
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'No valid data received']);
    }
} else {
    // Responde com erro se o método não for POST
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
}
