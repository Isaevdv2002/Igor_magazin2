<?php

// Подключение PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../core/phpmailer/PHPMailerAutoload.php';

// Загрузка конфигурационных данных
$data = json_decode(file_get_contents('../core/data.json'), true);

// Проверяем метод запроса
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Получаем данные из формы и защищаем от XSS-атак
    $name = htmlspecialchars($_POST["user_name"]);
    $phone = htmlspecialchars($_POST["user_phone"]);
    $products = json_decode($_POST["user_products"], true);

    // Проверяем данные на пустоту
    if (empty($name) || empty($phone) || empty($products)) {
        die("Не все данные были заполнены.");
    }

    // Инициализация PHPMailer с обработкой исключений
    $mail = new PHPMailer(true);

    try {
        // Настройки SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.mail.ru';
        $mail->SMTPAuth = true;
        $mail->Username = 'zayavka_s_sayta01@mail.ru';
        $mail->Password = 'nexeVWiMrK9wwXj6pDVM';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Отправитель и получатель письма
        $mail->setFrom('zayavka_s_sayta01@mail.ru');
        $mail->addAddress('i.isaeww27@gmail.com');
        $mail->isHTML(true);

        // Формируем тему и тело письма
        $mail->Subject = 'Заявка с сайта';
        $mail->Body = "$name оставил заявку, его телефон $phone<br>Ему требуется: " . implode(", ", $products);
        $mail->AltBody = '';

        // Отправляем письмо
        $mail->send();

        // Выводим сообщение об успешной отправке
        echo 'Письмо успешно отправлено';

    } catch (Exception $e) {
        echo "Письмо не было отправлено. Ошибка: {$mail->ErrorInfo}";
    }

} else {
    echo "Неверный запрос";
}

?>


