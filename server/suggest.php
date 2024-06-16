<?php
$filename = '../../ftp/price.xml';
$xml = simplexml_load_file($filename);

$query = isset($_GET['query']) ? $_GET['query'] : '';
$suggestions = [];

if (!empty($query)) {
    $query = mb_strtolower($query, 'UTF-8'); // Преобразуем запрос к нижнему регистру

    foreach ($xml->position as $position) {
        $productName = mb_strtolower((string)$position->name, 'UTF-8'); // Преобразуем имя товара к нижнему регистру
        // Проверяем совпадение с названием товара
        if (stripos($productName, $query) !== false) {
            $suggestions[] = (string)$position->name;
        }

        // Проверяем совпадение с категорией и подкатегорией
        $categories = explode('/', (string)$position->nomenclatureGroup);
        foreach ($categories as $category) {
            $category = mb_strtolower($category, 'UTF-8'); // Преобразуем категорию к нижнему регистру
            if (stripos($category, $query) !== false) {
                $suggestions[] = $category;
            }
        }
    }
}

// Убираем дубликаты и ограничиваем количество предложений
$suggestions = array_unique($suggestions);
$suggestions = array_slice($suggestions, 0, 10);

echo json_encode($suggestions);
?>

