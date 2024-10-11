<?php
// Defina o caminho do arquivo que deseja remover
$zipFilename = 'C:\xamp 8\htdocs\dashboard\minerador\minerados_links.zip';

// Verifique se o arquivo existe
if (file_exists($zipFilename)) {
    echo json_encode(["status" => "approved"]);
    exit;
} else {
    echo json_encode(["status" => "not_found"]);
}

?>
