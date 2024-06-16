<?php
// Функция для поиска имени товара по его идентификатору
function findProductNameById($xml, $productId) {
    foreach ($xml->position as $position) {
        if ((string) $position->id === $productId) {
            return (string) $position->name;
        }
    }
    return null; // Если товар с таким идентификатором не найден
}

// Загрузка XML документа для получения информации о товарах
$xml = simplexml_load_file('../../ftp/price.xml');
if ($xml === false) {
    echo json_encode(["error" => "Error loading XML file."]);
    exit;
}

$cart = json_decode($_POST['cart'] ?? '[]', true);
$productNames = [];

foreach ($cart as $productId) {
    $productName = findProductNameById($xml, $productId);
    if ($productName !== null) {
        $productNames[] = $productName;
    } else {
        $productNames[] = "Товар не найден";
    }
}

echo json_encode($productNames);
?>





