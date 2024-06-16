<?php
// Обработка отправки формы для контактных данных
if (isset($_POST['saveFormContact'])) {
    // Оповещение об успешном сохранении
    $notification = 'Данные успешно сохранены';
}

if (isset($_POST['saveFormPriceList'])) {
    // Оповещение об успешном сохранении
    $notification = 'Данные успешно сохранены';
}

if (isset($_POST['saveFormText'])) {
    // Оповещение об успешном сохранении
    $notification = 'Данные успешно сохранены';
}

if (isset($_POST['resetStatistics'])) {
    // Отправка запроса на сброс статистики
    $resetUrl = 'https://ensoez.ru/masl/admin/template/782391047201947obufeyueowybf1.php?reset=true';
    file_get_contents($resetUrl);

    // Оповещение об успешном сбросе
    $notification = 'Статистика успешно сброшена';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="template/admin.css">
    <script src="template/scriptotv.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<script>
    // Функция для закрытия модального окна
    function closeModal() {
        var modal = document.getElementById('myModal');
        modal.style.display = 'none';
    }

    // Закрыть модальное окно через 2 часа и перенаправить на logout.php
    setTimeout(function () {
        closeModal();
        window.location.href = 'template/logout.php';
    }, 2 * 60 * 60 * 1000); // 2 часа в миллисекундах
</script>

<body>


</style>
</style>
<div class="admin-panel">
<div class="notification-container <?php echo isset($notification) ? 'show-notification' : ''; ?>">
    <?php if (isset($notification)): ?>
        <div class="notification"><?= $notification ?></div>
    <?php endif; ?>
</div>
    <div class="admin-header">

        <div class="admin-buttons">
            <button id="contactBtn" class="blue-btn" >
                <img src="template/setting.svg" alt="Настройки" style="width: 30px; height: 30px;">
            
            <button id="textBtn" class="blue-btn">Текст</button>

            
        </div>
    </div>


<div  id="searchContainer" class="search-container">
    <input type="text" id="searchInput" placeholder="Поиск...">
    <button id="searchButton">
        <svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20">
            <defs>
                <style>.cls-1{fill: #555555;}</style>
            </defs>
            <g id="search">
                <path class="cls-1" d="M28.7,27.29l-9.76-9.56A9,9,0,1,0,12,21a8.92,8.92,0,0,0,5.49-1.89l9.81,9.6a1,1,0,0,0,1.41,0A1,1,0,0,0,28.7,27.29ZM5,12a7,7,0,1,1,7,7A7,7,0,0,1,5,12Z"/>
            </g>
        </svg>
    </button>
</div>

    <!-- Надпись при невыбранной категории -->
    <p class="category-select-label">Выберите категорию или нажмите
      <a href="template/logout.php" class="cancel-btn">Выйти</a></p>
         

<form action="#" method="post" class="stat-fields">
    <div style="display: flex; flex-direction: column; align-items: center; height: 230px;">
        <iframe src="https://ensoez.ru/masl/admin/template/782391047201947obufeyueowybf1.php" width="auto" height="250" frameborder="0"></iframe>
    </div>
    <div style="display: flex; flex-direction: column; align-items: center; height: 230px;">
        <iframe src="https://ensoez.ru/masl/admin/template/782391047201947obufeyueowybf2.php" width="auto" height="250" frameborder="0"></iframe>
    </div>
    <button type="submit" name="resetStatistics" class="reset-btn">Сбросить просмотры</button>
    <a href="template/logout.php" class="cancel-btn">Выйти</a>
</form>

      <form action="#" method="post" class="contact-fields">
    <div class ="pass">
        <label>Логин</label>
        <input type="text" name="user" required value="<?= htmlspecialchars($data['user']) ?>"><br>
        
        <label>Пароль</label>
        <input type="text" name="pass" required value="<?= htmlspecialchars($data['pass']) ?>"><br>
    </div>
          
        <label>Изменение почты</label>
        <input type="text" name="sendMail" required value="<?= htmlspecialchars($data['sendMail']) ?>"><br>

        <label>Номера телефона</label>
        <input type="text" name="phoneNumber" required value="<?= htmlspecialchars($data['phoneNumber']) ?>"><br>
        
        <label>Точный адрес</label>
        <input type="text" name="map" required value="<?= htmlspecialchars($data['map']) ?>"><br>

        <label>Способ получения уведомлений:</label>
        <div class="toggle-switch">
            <input type="radio" id="telegramOnly" name="notificationMethod" value="telega" <?php echo ($data['notificationMethod'] === 'telega') ? 'checked' : ''; ?>>
            <label for="telegramOnly" class="telegram-label">Телеграм</label>

            <input type="radio" id="telegramAndEmail" name="notificationMethod" value="telegaPochta" <?php echo ($data['notificationMethod'] === 'telegaPochta') ? 'checked' : ''; ?>>
            <label for="telegramAndEmail" class="telegram-email-label">Телеграм и почта</label>

            <input type="radio" id="emailOnly" name="notificationMethod" value="pochta" <?php echo ($data['notificationMethod'] === 'pochta') ? 'checked' : ''; ?>>
            <label for="emailOnly" class="email-label">Почта</label>
        </div>

        <div id="telegramFields" style="display: none;">
            <label>Токен Телеграма:</label>
            <input type="text" id="telegramToken" name="telegramToken" value="<?= htmlspecialchars($data['telegramToken']) ?>"><br>

            <label>ID чата в Телеграме:</label>
            <input type="text" id="telegramChatId" name="telegramChatId" value="<?= htmlspecialchars($data['telegramChatId']) ?>"><br>

        </div>

        <div id="emailFields" style="display: none;">
            <label>Email:</label>
            <input type="text" id="emailInput" name="emailInput" value="<?= htmlspecialchars($data['emailInput']) ?>"><br>
        </div>

<div id="myModal" class="modal">
  <span class="close" onclick="closeModal()">&times;</span>
  <img class="modal-content" id="modalImg">
</div>

<script>
  function openModal(imageSrc) {
    var modal = document.getElementById('myModal');
    var modalImg = document.getElementById('modalImg');
    modal.style.display = 'block';
    modalImg.src = imageSrc;
  }

  function closeModal() {
    var modal = document.getElementById('myModal');
    modal.style.display = 'none';
  }

  // Добавлен обработчик для закрытия модального окна при клике в любое место на экране
  window.onclick = function(event) {
    var modal = document.getElementById('myModal');
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  };
</script>

          

        <div class="instructions">
            <button id="instructionButton" class="blue-btn" data-target="instructionContent">Инструкция по уведомлениям</button>
            <div id="instructionContent" class="instruction-content" style="display: none;">
                    <h3 style="text-align: center;">Инструкция по уведомлениям</h3>
                    <p style="text-align: center;">Для получения уведомлений сначала нажмите на кнопку куда бы вы хотели получать уведомления.</p>

                    <h3 style="text-align: center;">Уведомления по почте</h3>
                    <p style="text-align: center;">Для получения уведомлений на почту просто введите ваш email в поле ввода</p>
                
                    <h3 style="text-align: center;">Уведомления в Телеграм</h3>
                    <p style="text-align: center;">Для получения уведомлений в телеграм:</p>
                    <ol class="ol-class">
                        <li class="step-images" onclick="openModal('template/jpg/1.jpg')"><img src="template/jpg/1.jpg">Зайдите в телеграм и откройте бота <a href="https://t.me/BotFather" target="_blank">@BotFather</a></li>
                        <li class="step-images" onclick="openModal('template/jpg/2.jpg')"><img src="template/jpg/2.jpg">Создайте нового бота, с помощью кнопки/команды /newbot.</li>
                        <li class="step-images" onclick="openModal('template/jpg/3.jpg')"><img src="template/jpg/3.jpg">Далее нужно выбрать любое имя для Вашего бота. <br>После этого следует дать ещё одно пользовательское имя с окончанием "bot". Например Tetris_bot или Tetrisbot </li>
                        <li class="step-images" onclick="openModal('template/jpg/4.jpg')"><img src="template/jpg/4.jpg">После успешного создания бота, для начала нужно его запустить (обведено зелёным цветом), перейдите по этой ссылке и нажмите "start" <br>Далее необходимо скопировать его токен (обведено красным цветом).</li>
                        <li><br>Готово! Вставьте токен вашего бота в поле ввода "Токен бота" в административной панели</li>
                        <li><br>Для получения вашего ID, перейдите в бота <a href="https://t.me/getmyid_bot" target="_blank">@getmyid_bot</a></li>
                        <li><br>Скопируйте ваш ID и вставьте в поле ввода "ID чата в Телеграме" в административной панели.</li>
                        <li><br>В конце не забудьте сохранить изменения в административной панели, нажав на кнопку "Сохранить"</li>
                    </ol>
            </div>
        </div>
        <input type="submit" name="saveFormContact" value="Сохранить">
        <a href="template/logout.php" class="cancel-btn">Выйти</a>
    </form>
   <style>
    label {
        display: block;
        margin-bottom: 5px;
    }

    textarea {
        width: 100%;
        height: 100px;
        border-radius: 15px;
        resize: none;
        border: 1px solid #ccc;
        transition: border-color 0.3s;
    }

    textarea:focus {
        outline: 2px solid rgb(211, 211, 211);
    }
</style>
    <form action="#" method="post" class="text-fields">
        <!-- Поля для ввода текста -->
        <label>О компании</label>
        <textarea name="aboutMe" required style="height: 100px; resize: none;"><?= htmlspecialchars($data['aboutMe']) ?></textarea><br>
    
        <label>Город</label>
        <input type="text" name="region" required value="<?= htmlspecialchars($data['region']) ?>"><br>

        <label>График работы</label>
        <input type="text" name="grafic" required value="<?= htmlspecialchars($data['grafic']) ?>"><br>
    
        <input type="submit" name="../saveFormText" value="Сохранить">
        
        <a href="template/logout.php" class="cancel-btn">Выйти</a>
    </form>

      <div class="scroll-btn" id="scrollUp">&#9650;</div>
    <div class="scroll-btn" id="scrollDown">&#9660;</div>

    <div class="content">
      
        <form action="#" method="post" class="price-list-fields">
            <ol class="ol-class">
                
        <li>
                <h1>Добавление и удаление товаров</h1>
    <label for="productCategory">Категория товара:</label>
<select id="productCategory">
    <option value="подарочные_наборы">Подарочные наборы</option>
    <option value="фарфоровые_сервизы">Фарфоровые сервизы</option>
    <option value="техника">Техника</option>
</select>

    <label for="productName">Название товара:</label>
    <input type="text" id="productName">
    <label for="productName">Описание товара:</label>
    <input type="text" id="productDescript">
    <label for="productPrice">Цена товара:</label>
    <input type="text" id="productPrice">
<label for="productImage1">Фото товара 1:</label>
<input type="file" id="productImage1" name="productImage1">

<label for="productImage2">Фото товара 2:</label>
<input type="file" id="productImage2" name="productImage2">

<label for="productImage3">Фото товара 3:</label>
<input type="file" id="productImage3" name="productImage3">

    <button class="list-save" onclick="addProduct()">Добавить товар</button>

    <h2>Список товаров</h2>
    <ul id="existingProducts"></ul>
        
        <a href="template/logout.php" class="cancel-btn">Выйти</a>
            </form>
          </div>
              </div>
          <script>
          
document.getElementById("scrollUp").addEventListener("click", function() {
    scrollToTop();
});

document.getElementById("scrollDown").addEventListener("click", function() {
    scrollToBottom();
});

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function scrollToBottom() {
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
}

          </script>
          <style>
                              body {
    margin: 0;
    font-family: Arial, sans-serif;
}

.scroll-btn {
    position: fixed;
    bottom: 20px;
    width: 40px;
    height: 40px;
    background-color: #3498db94;
    color: #fff;
    text-align: center;
    line-height: 40px;
    font-size: 20px;
    cursor: pointer;
    border-radius: 50%;
}

#scrollUp {
    right: 20px;
}

#scrollDown {
    right: 80px;
}
          </style>

<footer style="text-align: center; margin-top: 7px; position: relative; margin-bottom: 5px;">
    <a href="https://ensoez.ru" style="text-decoration: none; color: inherit;">
        <div style="display: inline-block; background-color: #3498db; padding: 5px 10px; border-radius: 20px; color: #ffffff;">
            Ensoez<span style="color: #8eff8e;">Tech</span>
        </div> - Version 4.1.2
    </a>
</footer>
 <script src="../script.js"></script>
</body>
</html>