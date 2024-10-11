<?php

// Arquivos onde o progresso e o número de páginas são salvos
$progressFile = 'progress.txt';
$pageCountFile = 'page_count.txt';

// Verifique se o arquivo de progresso existe
if (file_exists($progressFile) && file_exists($pageCountFile)) {
    // Leia o conteúdo dos arquivos
    $progress = file_get_contents($progressFile);
    $pageCount = file_get_contents($pageCountFile);
    
    // Retorne o progresso e o número de páginas em formato JSON
    echo json_encode([
        'progress' => number_format($progress, 2), // Progresso formatado com 2 casas decimais
        'pages' => intval($pageCount) // Número de páginas processadas
    ]);
} else {
    // Caso os arquivos de progresso ou de contagem de páginas não existam, retorne 0
    echo json_encode([
        'progress' => 0,
        'pages' => 0
    ]);
}
