<?php
include_once('esp-database.php');

header('Content-Type: application/json');

// Cuántas lecturas traer
$readings_count = isset($_GET['readingsCount']) ? intval($_GET['readingsCount']) : 20;

// Última lectura
$last_reading = getLastReadings();

// Mínimos, máximos, promedios
$min_temp = minReading($readings_count, 'value1');
$max_temp = maxReading($readings_count, 'value1');
$avg_temp = avgReading($readings_count, 'value1');
$min_humi = minReading($readings_count, 'value2');
$max_humi = maxReading($readings_count, 'value2');
$avg_humi = avgReading($readings_count, 'value2');

// Últimos registros
$result = getAllReadings($readings_count);
$history = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    $result->free();
}

// Devolver todo en un solo JSON
echo json_encode([
    'last' => $last_reading,
    'min_temp' => $min_temp['min_amount'],
    'max_temp' => $max_temp['max_amount'],
    'avg_temp' => round($avg_temp['avg_amount'], 2),
    'min_humi' => $min_humi['min_amount'],
    'max_humi' => $max_humi['max_amount'],
    'avg_humi' => round($avg_humi['avg_amount'], 2),
    'history' => $history
]);
?>
