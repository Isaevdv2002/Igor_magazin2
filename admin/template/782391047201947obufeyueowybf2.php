<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>График посетителей по дням недели</title>
    <!-- Подключение библиотеки Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Элемент для отображения графика -->
<canvas id="visitorChart" width="600" height="400"></canvas>

<script>
<?php
$counterFile = 'countercook_by_day.json';

if (isset($_GET['update']) && $_GET['update'] === 'true') {
    if (!isset($_COOKIE['userCounted'])) {
        $currentCounts = file_exists($counterFile) ? json_decode(file_get_contents($counterFile), true) : [];
        $currentDay = date('N');
        $currentCounts[$currentDay] = isset($currentCounts[$currentDay]) ? $currentCounts[$currentDay] + 1 : 1;
        file_put_contents($counterFile, json_encode($currentCounts));
        setcookie('userCounted', 'true', time() + 5);
    }
}

$currentCounts = file_exists($counterFile) ? json_decode(file_get_contents($counterFile), true) : [];
$chartData = array_fill(1, 7, 0);

// Дополним метки для всех дней недели
$daysOfWeekLabels = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

foreach ($currentCounts as $day => $count) {
    $chartData[$day] = $count;
}

$chartData['Пн'] = $chartData[1];
unset($chartData[1]);

$chartData['Вт'] = $chartData[2];
unset($chartData[2]);

$chartData['Ср'] = $chartData[3];
unset($chartData[3]);

$chartData['Чт'] = $chartData[4];
unset($chartData[4]);

$chartData['Пт'] = $chartData[5];
unset($chartData[5]);

$chartData['Сб'] = $chartData[6];
unset($chartData[6]);

$chartData['Вс'] = $chartData[7];
unset($chartData[7]);
?>

function updateChart(counts) {
    var ctx = document.getElementById('visitorChart').getContext('2d');

    var data = {
        labels: <?php echo json_encode($daysOfWeekLabels); ?>,
        datasets: [{
            label: 'Количество посетителей',
            data: counts,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    var options = {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        indexAxis: 'x',
        elements: {
            bar: {
                borderWidth: 2,
                borderRadius: 5,
            }
        },
        categoryPercentage: 0.8,
    };

    var myChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options
    });
}

updateChart(<?php echo json_encode($chartData); ?>);

</script>

</body>
</html>  