<?php

$data = json_decode(file_get_contents('../core/data.json'), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["user_name"];
    $phone = $_POST["user_phone"];
    $products = json_decode($_POST["user_products"], true);
    $source = $_POST["source"]; // Новое поле

    echo "Имя: " . $name . "<br>";
    echo "Телефон: " . $phone . "<br>";

    $isEmptyProducts = empty($products);

    $chatId = $data['telegramChatId']; // Замените на реальный ID чата
    $token = $data['telegramToken']; // Замените на реальный токен бота
    $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';

    if ($isEmptyProducts) {
        // Отправляем уведомление в телеграм о запросе обратного звонка
        $message = "Запрос обратного звонка:\n";
        $message .= "Имя: $name\nТелефон: $phone";

        $data = array(
            'chat_id' => $chatId,
            'text' => $message
        );
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        // Обработка результата, если нужно
        echo $result;
    } else {
        // Отправляем сообщение в телеграм с информацией о клиента и продуктах
        if ($source === "connect") {
            $message = "Обратный звонок, в корзине:\n";
        } elseif ($source === "zakaz") {
            $message = "Заказ товара:\n";
        } else {
            $message = "";
        }
        $message .= "Имя: $name\nТелефон: $phone\n\n";

        // Создаем переменную для разделителя между товарами
        $divider = "__________________________\n";

        // Добавляем информацию о товарах
        foreach ($products as $index => $productId) {
            $productNumber = $index + 1; // Увеличиваем индекс на 1, чтобы начать счет с 1, а не с 0
            $message .= "Товар $productNumber:\n";
            // Здесь вы можете добавить логику для получения подробной информации о товаре по его ID
            $message .= "ID: $productId\n";
            $message .= "\n"; // Добавляем отступ строки между товарами
            if ($index < count($products) - 1) {
                $message .= $divider;
            }
        }

        $data = array(
            'chat_id' => $chatId,
            'text' => $message
        );
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        // Обработка результата, если нужно
        echo $result;
    }
} else {
    // Обработка других методов запроса или прямого доступа к скрипту
    echo "Неверный запрос";
}
?>


