<?php
session_start();

// Уничтожаем сессию
session_destroy();

// Перенаправляем на страницу входа
header("Location: https://ensoez.ru/masl/admin");
exit();
?>