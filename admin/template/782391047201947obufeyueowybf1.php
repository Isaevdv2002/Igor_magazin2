<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Подключение библиотеки Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Элемент для отображения графика -->
<canvas id="visitorChart" width="<?php echo isset($_GET['width']) ? intval($_GET['width']) : 400; ?>" height="<?php echo isset($_GET['height']) ? intval($_GET['height']) : 300; ?>"></canvas>

<script>
<?php
// Сброс статистики, если параметр reset=true присутствует
if (isset($_GET['reset']) && $_GET['reset'] === 'true') {
    // Открываем файл с количеством посетителей
    $counterFile = 'counter.txt';

    
    file_put_contents($counterFile, '0');

    // Перенаправление пользователя после сброса
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Файл для хранения количества посетителей
$counterFile = 'counter.txt';

// Сброс статистики при отправке формы
if (isset($_POST['resetStatistics'])) {
    // Удаление файла с количеством посетителей
    if (file_exists($counterFile)) {
        unlink($counterFile);
    }
    // Перенаправление пользователя после сброса
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Если параметр update=true присутствует, обновляем количество посетителей
if (isset($_GET['update']) && $_GET['update'] === 'true') {
    // Получение текущего количества посетителей
    $currentCount = file_exists($counterFile) ? intval(file_get_contents($counterFile)) : 0;

    // Инкремент количества посетителей
    $currentCount++;

    // Запись нового значения обратно в файл
    file_put_contents($counterFile, $currentCount);
}

// Получение текущего количества посетителей
$currentCount = file_exists($counterFile) ? intval(file_get_contents($counterFile)) : 0;
?>

// Функция для отображения графика
function updateChart(count) {
    var ctx = document.getElementById('visitorChart').getContext('2d');
    
    // Определение данных для столбчатой диаграммы
    var data = {
        labels: ['Просмотры'],
        datasets: [{
            label: 'Количество просмотров сайта',
            data: [count],
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    // Определение опций для графика
    var options = {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        elements: {
            bar: {
                borderRadius: 5, // Установка радиуса скругления углов
            }
        }
    };

    // Создание и обновление столбчатой диаграммы
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options
    });
}

// Обновление столбчатой диаграммы с текущим количеством посетителей
updateChart(<?php echo $currentCount; ?>);
</script>

</body>
</html>
