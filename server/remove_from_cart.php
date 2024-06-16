<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем идентификатор товара из POST-запроса
    $productId = $_POST['productId'];

    // Получаем текущий массив товаров в корзине из куки или создаем пустой массив
    $cart = json_decode($_COOKIE['cart'] ?? '[]', true);

    // Проверяем, что $cart является массивом
    if (!is_array($cart)) {
        $cart = [];
    }

    // Удаляем идентификатор товара из массива корзины
    if (($key = array_search($productId, $cart)) !== false) {
        unset($cart[$key]);
    }

    // Кодируем массив в формат JSON и сохраняем его в куки
    setcookie('cart', json_encode(array_values($cart)), time() + (86400 * 30), "/"); // Сохраняем на 30 дней

    echo json_encode(['success' => true]);
} else {
    // Возвращаем ошибку, если запрос не является POST-запросом
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
}
?>
