<?php
// rota_get_csv.php
header('Content-Type: application/json');

$csvFile = 'dados_em_tempo_real.csv';

$rows = [];

if (file_exists($csvFile)) {
    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $rows[] = $data;
        }
        fclose($handle);
    }
}

echo json_encode($rows);

?>