<?php
include_once('esp-database.php');

// Definir cuántas lecturas mostrar
if (isset($_GET["readingsCount"]) && is_numeric($_GET["readingsCount"])) {
    $readings_count = intval($_GET["readingsCount"]);
} else {
    $readings_count = 20;
}

// Cargar valores iniciales para primera carga
$last_reading = getLastReadings();
$last_reading_temp = $last_reading["value1"];
$last_reading_humi = $last_reading["value2"];
$last_reading_time = $last_reading["reading_time"];

$min_temp = minReading($readings_count, 'value1');
$max_temp = maxReading($readings_count, 'value1');
$avg_temp = avgReading($readings_count, 'value1');

$min_humi = minReading($readings_count, 'value2');
$max_humi = maxReading($readings_count, 'value2');
$avg_humi = avgReading($readings_count, 'value2');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ESP Weather Station</title>
    <link rel="stylesheet" href="esp-style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<header class="header">
    <h1>📊 ESP Weather Station</h1>
    <form method="get">
        <input type="number" name="readingsCount" min="1" placeholder="Number of readings (<?php echo $readings_count; ?>)">
        <input type="submit" value="UPDATE">
    </form>
</header>

<p id="lastReading">Last reading: <?php echo $last_reading_time; ?></p>

<section class="content">
    <div class="box gauge--1">
        <h3>TEMPERATURE</h3>
        <div class="mask">
            <div class="semi-circle"></div>
            <div class="semi-circle--mask"></div>
        </div>
        <p style="font-size: 30px;" id="temp"><?php echo $last_reading_temp; ?> ºC</p>
        <table id="tempStats" cellspacing="5" cellpadding="5">
            <tr><th colspan="3">Temperature <?php echo $readings_count; ?> readings</th></tr>
            <tr><td>Min</td><td>Max</td><td>Average</td></tr>
            <tr>
                <td><?php echo $min_temp['min_amount']; ?> &deg;C</td>
                <td><?php echo $max_temp['max_amount']; ?> &deg;C</td>
                <td><?php echo round($avg_temp['avg_amount'], 2); ?> &deg;C</td>
            </tr>
        </table>
    </div>

    <div class="box gauge--2">
        <h3>HUMIDITY</h3>
        <div class="mask">
            <div class="semi-circle"></div>
            <div class="semi-circle--mask"></div>
        </div>
        <p style="font-size: 30px;" id="humi"><?php echo $last_reading_humi; ?> %</p>
        <table id="humiStats" cellspacing="5" cellpadding="5">
            <tr><th colspan="3">Humidity <?php echo $readings_count; ?> readings</th></tr>
            <tr><td>Min</td><td>Max</td><td>Average</td></tr>
            <tr>
                <td><?php echo $min_humi['min_amount']; ?> %</td>
                <td><?php echo $max_humi['max_amount']; ?> %</td>
                <td><?php echo round($avg_humi['avg_amount'], 2); ?> %</td>
            </tr>
        </table>
    </div>
</section>

<h2>📈 Historical Data (Temperature and Humidity)</h2>
<canvas id="chart" width="400" height="200"></canvas>

<h2>View Latest <?php echo $readings_count; ?> Readings</h2>
<table cellspacing="5" cellpadding="5" id="tableReadings">
    <thead>
        <tr>
            <th>ID</th>
            <th>Sensor</th>
            <th>Location</th>
            <th>Value 1</th>
            <th>Value 2</th>
            <th>Value 3</th>
            <th>Timestamp</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = getAllReadings($readings_count);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                    <td>' . $row["id"] . '</td>
                    <td>' . $row["sensor"] . '</td>
                    <td>' . $row["location"] . '</td>
                    <td>' . $row["value1"] . '</td>
                    <td>' . $row["value2"] . '</td>
                    <td>' . $row["value3"] . '</td>
                    <td>' . $row["reading_time"] . '</td>
                </tr>';
            }
            $result->free();
        }
        ?>
    </tbody>
</table>

<script>
var ctx = document.getElementById('chart').getContext('2d');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Temperature (°C)',
            data: [],
            borderColor: 'red',
            backgroundColor: 'rgba(255,0,0,0.1)',
            yAxisID: 'y-axis-temp'
        }, {
            label: 'Humidity (%)',
            data: [],
            borderColor: 'blue',
            backgroundColor: 'rgba(0,0,255,0.1)',
            yAxisID: 'y-axis-humi'
        }]
    },
    options: {
        scales: {
            yAxes: [
                { id: 'y-axis-temp', type: 'linear', position: 'left', ticks: { beginAtZero: true } },
                { id: 'y-axis-humi', type: 'linear', position: 'right', ticks: { beginAtZero: true } }
            ],
            xAxes: [{ type: 'time', time: { unit: 'minute' } }]
        }
    }
});

function setTemperature(curVal){
    var newVal = scaleValue(curVal, [-5, 38], [0, 180]);
    $('.gauge--1 .semi-circle--mask').attr('style', 'transform: rotate(' + newVal + 'deg);');
    $("#temp").text(curVal + ' ºC');
}

function setHumidity(curVal){
    var newVal = scaleValue(curVal, [0, 100], [0, 180]);
    $('.gauge--2 .semi-circle--mask').attr('style', 'transform: rotate(' + newVal + 'deg);');
    $("#humi").text(curVal + ' %');
}

function scaleValue(value, from, to) {
    var scale = (to[1] - to[0]) / (from[1] - from[0]);
    var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
    return ~~(capped * scale + to[0]);
}

function updateDashboard() {
    $.ajax({
        url: 'esp-ajax-full-data.php?readingsCount=20',
        method: 'GET',
        success: function(data) {
            setTemperature(data.last.value1);
            setHumidity(data.last.value2);
            $("#lastReading").text('Last reading: ' + data.last.reading_time);

            $('#tempStats tr:nth-child(3)').html(`
                <td>${data.min_temp} &deg;C</td>
                <td>${data.max_temp} &deg;C</td>
                <td>${data.avg_temp} &deg;C</td>
            `);

            $('#humiStats tr:nth-child(3)').html(`
                <td>${data.min_humi} %</td>
                <td>${data.max_humi} %</td>
                <td>${data.avg_humi} %</td>
            `);

            let tableHtml = '';
            data.history.forEach(function(row) {
                tableHtml += `
                    <tr>
                        <td>${row.id}</td>
                        <td>${row.sensor}</td>
                        <td>${row.location}</td>
                        <td>${row.value1}</td>
                        <td>${row.value2}</td>
                        <td>${row.value3}</td>
                        <td>${row.reading_time}</td>
                    </tr>
                `;
            });
            $('#tableReadings tbody').html(tableHtml);

            var times = data.history.map(row => row.reading_time);
            var temps = data.history.map(row => parseFloat(row.value1));
            var hums = data.history.map(row => parseFloat(row.value2));

            chart.data.labels = times;
            chart.data.datasets[0].data = temps;
            chart.data.datasets[1].data = hums;
            chart.update();
        }
    });
}

updateDashboard();
setInterval(updateDashboard, 5000);
</script>

</body>
</html>
