<?php
// Поиск по названию товара и номенклатурной группе
$searchQuery = isset($_GET['search']) ? mb_strtolower($_GET['search']) : '';
$searchResults = [];

if (!empty($searchQuery)) {
    foreach ($xml->position as $position) {
        $productName = mb_strtolower((string)$position->name);
        $nomenclatureGroup = mb_strtolower((string)$position->nomenclatureGroup);
        
        // Проверяем совпадения как по названию товара, так и по номенклатурной группе
        if (stripos($productName, $searchQuery) !== false || stripos($nomenclatureGroup, $searchQuery) !== false) {
            $searchResults[] = $position;
        }
    }
}

?>


<?php
$filename = '../ftp/price.xml';

// Читаем содержимое файла
$fileContents = file_get_contents($filename);

// Массив для заменяемых значений и их новых значений
$replacements = array(
    '66279e36c9b542063b48dade' => 'price1',
    '66279e36c9b542063b48dadf' => 'price2',
    '66279e36c9b542063b48dae0' => 'price3'
);

// Заменяем все вхождения в соответствии с массивом $replacements
$fileContents = str_replace(array_keys($replacements), $replacements, $fileContents);

// Сохранение изменений обратно в файл, если это необходимо
file_put_contents($filename, $fileContents);
?>
<?php
session_start();

// Получаем текущий массив товаров в корзине из куки или создаем пустой массив
$cart = json_decode($_COOKIE['cart'] ?? '[]', true);

if (!is_array($cart)) {
    $cart = [];
}

// Загрузка XML документа для получения информации о товарах
$xml = simplexml_load_file('../ftp/price.xml');
if ($xml === false) {
    echo "Error loading XML file. Ведутся технические работы";
    exit;
}

$productsInCart = [];

foreach ($xml->position as $position) {
    if (in_array((string) $position->id, $cart)) {
        $productsInCart[] = $position;
    }
}
?>
<?php
$data = json_decode(file_get_contents( 'core/data.json'),1);
?>
<?php

// Load the XML document
$xml = simplexml_load_file('../ftp/price.xml');
if ($xml === false) {
    echo "Error loading XML file. Ведутся технические работы";
    exit;
}

// Function to create a nested category list
function buildCategoryTree($xml) {
    $categories = [];
    
    // Iterate through all positions and create a category tree
    foreach ($xml->position as $position) {
        // Skip positions without a nomenclatureGroupId
        if (empty((string) $position->nomenclatureGroupId)) {
            continue;
        }

        $groups = explode('/', trim((string) $position->nomenclatureGroup, '/'));
        $current = &$categories;
        
        foreach ($groups as $group) {
            if (!isset($current[$group])) {
                $current[$group] = [];
            }
            $current = &$current[$group];
        }
    }
    
    return $categories;
}

// Build the category tree
$categories = buildCategoryTree($xml);

// Recursive function to display the category tree as HTML
function displayCategories($categories, $basePath = '') {
    echo '<ul class="categories">';
    foreach ($categories as $category => $subcategories) {
        $path = $basePath === '' ? $category : $basePath . '/' . $category;
        echo '<li>';
        echo '<a href="index.php?category=' . urlencode($path) . '">' . htmlspecialchars($category) . '</a>';
        if (!empty($subcategories)) {
            displayCategories($subcategories, $path);
        }
        echo '</li>';
    }
    echo '</ul>';
}



// Get the category from the URL parameter
$category = isset($_GET['category']) ? $_GET['category'] : '';

?>

<!DOCTYPE html>
<html lang="ru">
    <head>
   <meta charset="utf-8">
<title>О нас</title>

<meta name="description" content="">
<meta name="keywords" content="">
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#EEF2E9 ">    
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="icon" type="image/png" href="images/logo.png">
<link href="css/styles.css" rel="stylesheet"> 
<link href="css/style.css" rel="stylesheet">   
<script src="js/common.js"></script>
<script src="js/jquery-3.6.4.min.js"></script>
<script src="js/jquery-3.6.1.min.js"></script>
<script src="js/jquery.contactus.js"></script>		
<script src="js/conscript.js"></script>
<link href="css/jquery.contactus.css" rel="stylesheet">
<link href="css/animate.min.css" rel="stylesheet">
<script>
// Функция для получения количества товаров в корзине с сервера
function updateCartCount() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'server/get_cart_count.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var count = parseInt(xhr.responseText); // Парсим ответ сервера в число
            var countElement = document.querySelector('.count.ms2_total_count');
            if (countElement) {
                countElement.textContent = count; // Обновляем содержимое элемента с классом count
            }
        } else if (xhr.readyState === 4) {
            console.log('Ошибка при получении количества товаров в корзине: ' + xhr.status);
        }
    };
    xhr.send();
}

// Вызываем функцию для обновления количества товаров в корзине при загрузке страницы и через определенные интервалы времени
updateCartCount();
setInterval(updateCartCount, 600); // Обновляем каждую минуту (60000 миллисекунд)




document.addEventListener('DOMContentLoaded', function() {
    // Раскрытие подкатегорий
    var categoryLinks = document.querySelectorAll('.sidebar ul.categories > li > a');
    categoryLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            var sublist = this.nextElementSibling;
            if (sublist) {
                event.preventDefault();
                sublist.style.display = (sublist.style.display === 'block') ? 'none' : 'block';
            }
        });
    });

    // Добавление в корзину
// Создаем пустой массив для хранения идентификаторов добавленных товаров
var addedProducts = [];

document.querySelectorAll('.add-to-cart-button').forEach(function(button) {
    button.addEventListener('click', function() {
        var productId = this.getAttribute('data-product-id');

        if (addedProducts.includes(productId)) {
            var modal = document.getElementById("modal2");
            var modalMessage = document.getElementById("modal-message2");
            modal.style.display = "block";
            modalMessage.textContent = 'Этот товар уже добавлен в корзину';
            setTimeout(function() {
                modal.style.display = "none";
            }, 1500);
            return;
        }

        console.log('ID товара:', productId);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'server/add_to_cart.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Ответ:', xhr.responseText);
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    addedProducts.push(productId);
                    var modal = document.getElementById("modal1");
                    var modalMessage = document.getElementById("modal-message1");
                    modal.style.display = "block";
                    modalMessage.textContent = 'Товар добавлен в корзину!';
                    setTimeout(function() {
                        modal.style.display = "none";
                    }, 1500);
                } else {
                    alert('Ошибка добавления в корзину.');
                }
            } else if (xhr.readyState === 4) {
                console.log('Ошибка: ' + xhr.status);
            }
        };
        xhr.send('productId=' + encodeURIComponent(productId));
    });
});
});

</script>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap">
<link rel="stylesheet" href="css/default_1.css" type="text/css">
<link rel="stylesheet" href="css/jquery.jgrowl.min.css" type="text/css">
<link rel="stylesheet" href="css/default.min.css" type="text/css">
<script type="text/javascript">msFavoritesConfig={"actionUrl":"\/assets\/components\/msfavorites\/action.php","ctx":"web","version":"2.1.4-beta","options":null};</script>
<link rel="stylesheet" href="css/default.css" type="text/css">
<script type="text/javascript">msOptionsPriceConfig={"assetsBaseUrl":"\/assets\/","assetsUrl":"\/assets\/components\/msoptionsprice\/","actionUrl":"\/assets\/components\/msoptionsprice\/action.php","allow_zero_cost":false,"allow_zero_old_cost":true,"allow_zero_mass":false,"allow_zero_article":false,"allow_zero_count":false,"allow_remains":false,"miniShop2":{"version":"3.0.7-pl"},"ctx":"web","version":"2.5.18-beta"};</script>
</head>

<body class="index-template">

    <div id="addToCartPopup" class="popup"></div>
<div id="confirmation-message" style="display: none;">Мы свяжемся с Вами!</div>
    <div class="main-wrapper">
        
<div id="modal1" class="modal1">
    <div class="modal-content1">
        <p id="modal-message1">Товар добавлен в корзину!</p>
    </div>
</div>
<div id="modal2" class="modal1">
    <div class="modal-content1">
        <p id="modal-message2">Этот товар уже добавлен в корзину</p>
    </div>
</div>
     <!-- Mobile menu -->    
    <div class="mobile-menu" id="mobmenu">  
        <div class="mobile-menu-inner">

		        <div class="menu-wrap-mobile">
                    <a href="/" class="menu disabled">
    		            <div class="menu-icon">
    		                <span class="top-line"></span>
    		                <span class="middle-line"></span>
    		                <span class="bottom-line"></span>
    		            </div>
                        Меню
    		        </a>
    		        <span class="dropdown-button"></span>
    		        <div class="menu-mobile-ul-wrap">
    <div class="sidebar menu_mini">
        <?php displayCategories($categories); ?>
    </div>
                    </div>
                </div>
		        <ul class="menu-nav"><li class="first">
                  <a href="index.php" data-hover="Главная">Главная</a>
                </li>
                <li>
                    <a href="#about" data-hover="Доставка и оплата">Доставка и оплата</a>
                </li>
                <li>
                    <a href="korzina.php" data-hover="Корзина">Корзина</a>
                </li>
            </ul>
                
                <div class="item-inner vis-xs">
			        
        			<a class="phone" href="tel:<?=$data['phoneNumber']?>">
            			<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.16301 2.61137C5.81393 2.70896 5.58044 2.84682 5.02104 3.28557C3.55905 4.43227 3.04816 5.08463 2.85354 6.05316C2.66566 6.98794 2.86193 7.73626 3.79446 9.64069C5.02432 12.1522 6.48185 14.2057 8.38676 16.1106C10.278 18.0019 12.3673 19.4881 14.8098 20.6798C16.795 21.6483 17.5233 21.8543 18.4087 21.6977C19.1589 21.565 19.7653 21.2001 20.4313 20.4803C20.9279 19.9438 21.5997 19.0825 21.7291 18.8164C22.0582 18.1397 21.9082 17.2928 21.3511 16.6833C21.1081 16.4174 20.6008 16.1 19.0286 15.23C16.5226 13.8435 16.4428 13.809 15.8253 13.8459C15.4013 13.8712 15.1537 13.9816 14.8738 14.2699C14.6516 14.499 14.484 14.8105 14.391 15.1678C14.2698 15.6331 14.145 15.7579 13.8007 15.7579C13.4852 15.7579 13.1906 15.5471 11.9028 14.3993C10.867 13.4763 9.00865 11.4578 8.80976 11.0399C8.61166 10.6235 8.79513 10.2752 9.27855 10.1501C10.3638 9.86921 10.8652 9.14874 10.6413 8.19193C10.5589 7.83952 10.4023 7.52813 9.83613 6.59007C9.6326 6.25285 9.20238 5.46596 8.88012 4.84144C8.24834 3.61712 7.95799 3.1808 7.59105 2.90424C7.17382 2.5898 6.63523 2.47932 6.16301 2.61137ZM6.29979 3.39718C5.81276 3.57835 4.33005 4.8413 3.97277 5.37929C3.4653 6.14344 3.4616 6.92612 3.95993 8.09836C6.28179 13.5599 10.2986 17.7153 15.6589 20.2011C17.4904 21.0504 18.0236 21.1316 18.9114 20.6964C19.3411 20.4857 19.9339 19.9084 20.5806 19.0709C21.1271 18.3631 21.2004 18.1361 21.0373 17.6573C20.8758 17.1833 20.6235 17.0133 17.6926 15.4029C16.4966 14.7458 16.2453 14.6355 15.9403 14.634C15.538 14.632 15.2871 14.8796 15.158 15.4063C15.0381 15.8956 14.7917 16.2338 14.399 16.4484C14.284 16.5113 14.1396 16.5313 13.802 16.5312C13.4243 16.5311 13.3247 16.5142 13.1457 16.42C12.3856 16.0196 9.97605 13.7462 8.75126 12.2736C8.01729 11.3912 7.92213 11.1974 7.95354 10.6485C7.97627 10.2516 8.05765 10.056 8.31105 9.78919C8.52223 9.56686 8.66332 9.49154 9.08674 9.37524C9.49774 9.26237 9.73249 9.11283 9.83374 8.89951C9.99944 8.55033 9.9049 8.26632 9.26838 7.20104C8.99037 6.73576 8.49926 5.84316 8.17699 5.21743C7.85473 4.59174 7.49613 3.96165 7.38012 3.81727C7.0564 3.41443 6.66387 3.26176 6.29979 3.39718Z" fill="black"></path>
                        </svg>
        				<span><?=$data['phoneNumber']?></span>
        			</a>
        			
			         <a href="#feedback" class="mini-button fancyboxModal">
			             
    					<span>Заказать звонок</span>
    				</a>
			    </div>
           
        </div>
    </div>

    <div class="overlay"></div>
    <!-- /. Mobile menu -->

<header class="header-wrapper">
	<div class="container">
		<div class="header-inner">
		    <div class="item menu-nav-wrapper">
		        <a href="/" class="menu disabled">
		            <div class="menu-icon">
		                <span class="top-line"></span>
		                <span class="middle-line"></span>
		                <span class="bottom-line"></span>
		            </div>
                    
		        </a>
		        
		        <ul class="menu-nav"><li class="first">
		          <a href="index.php" data-hover="Главная">Главная</a>
                </li>
                 <li>
                    <a href="#about" data-hover="Доставка и оплата">Доставка и оплата</a>
                </li>
                <li>
                    <a href="korzina.php" data-hover="Корзина">Корзина</a>
                </li>
            </ul>
		    </div>
            <div class="item">
            	<div class="logo-wrapper">
    	            <a class="logo">
    					<img src="images/logo.png" alt="Интернет-магазин">
    				</a>
    			</div>
        
            </div>
			<div class="item">
			    <div class="item-inner none-xs call-header">
			        
        			<a class="phone" href="tel:<?=$data['phoneNumber']?>">
            			<svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M16.1007 13.359L15.5719 12.8272H15.5719L16.1007 13.359ZM16.5562 12.9062L17.085 13.438H17.085L16.5562 12.9062ZM18.9728 12.5894L18.6146 13.2483L18.9728 12.5894ZM20.8833 13.628L20.5251 14.2869L20.8833 13.628ZM21.4217 16.883L21.9505 17.4148L21.4217 16.883ZM20.0011 18.2954L19.4723 17.7636L20.0011 18.2954ZM18.6763 18.9651L18.7459 19.7119H18.7459L18.6763 18.9651ZM8.81536 14.7266L9.34418 14.1947L8.81536 14.7266ZM4.00289 5.74561L3.2541 5.78816L3.2541 5.78816L4.00289 5.74561ZM10.4775 7.19738L11.0063 7.72922H11.0063L10.4775 7.19738ZM10.6342 4.54348L11.2346 4.09401L10.6342 4.54348ZM9.37326 2.85908L8.77286 3.30855V3.30855L9.37326 2.85908ZM6.26145 2.57483L6.79027 3.10667H6.79027L6.26145 2.57483ZM4.69185 4.13552L4.16303 3.60368H4.16303L4.69185 4.13552ZM12.0631 11.4972L12.5919 10.9654L12.0631 11.4972ZM16.6295 13.8909L17.085 13.438L16.0273 12.3743L15.5719 12.8272L16.6295 13.8909ZM18.6146 13.2483L20.5251 14.2869L21.2415 12.9691L19.331 11.9305L18.6146 13.2483ZM20.8929 16.3511L19.4723 17.7636L20.5299 18.8273L21.9505 17.4148L20.8929 16.3511ZM18.6067 18.2184C17.1568 18.3535 13.4056 18.2331 9.34418 14.1947L8.28654 15.2584C12.7186 19.6653 16.9369 19.8805 18.7459 19.7119L18.6067 18.2184ZM9.34418 14.1947C5.4728 10.3453 4.83151 7.10765 4.75168 5.70305L3.2541 5.78816C3.35456 7.55599 4.14863 11.144 8.28654 15.2584L9.34418 14.1947ZM10.7195 8.01441L11.0063 7.72922L9.9487 6.66555L9.66189 6.95073L10.7195 8.01441ZM11.2346 4.09401L9.97365 2.40961L8.77286 3.30855L10.0338 4.99296L11.2346 4.09401ZM5.73263 2.04299L4.16303 3.60368L5.22067 4.66736L6.79027 3.10667L5.73263 2.04299ZM10.1907 7.48257C9.66189 6.95073 9.66117 6.95144 9.66045 6.95216C9.66021 6.9524 9.65949 6.95313 9.659 6.95362C9.65802 6.95461 9.65702 6.95561 9.65601 6.95664C9.65398 6.95871 9.65188 6.96086 9.64972 6.9631C9.64539 6.96759 9.64081 6.97245 9.63599 6.97769C9.62634 6.98816 9.61575 7.00014 9.60441 7.01367C9.58174 7.04072 9.55605 7.07403 9.52905 7.11388C9.47492 7.19377 9.41594 7.2994 9.36589 7.43224C9.26376 7.70329 9.20901 8.0606 9.27765 8.50305C9.41189 9.36833 10.0078 10.5113 11.5343 12.0291L12.5919 10.9654C11.1634 9.54499 10.8231 8.68059 10.7599 8.27309C10.7298 8.07916 10.761 7.98371 10.7696 7.96111C10.7748 7.94713 10.7773 7.9457 10.7709 7.95525C10.7677 7.95992 10.7624 7.96723 10.7541 7.97708C10.75 7.98201 10.7451 7.98759 10.7394 7.99381C10.7365 7.99692 10.7335 8.00019 10.7301 8.00362C10.7285 8.00534 10.7268 8.00709 10.725 8.00889C10.7241 8.00979 10.7232 8.0107 10.7223 8.01162C10.7219 8.01208 10.7212 8.01278 10.7209 8.01301C10.7202 8.01371 10.7195 8.01441 10.1907 7.48257ZM11.5343 12.0291C13.0613 13.5474 14.2096 14.1383 15.0763 14.2713C15.5192 14.3392 15.8763 14.285 16.1472 14.1841C16.28 14.1346 16.3858 14.0763 16.4658 14.0227C16.5058 13.9959 16.5392 13.9704 16.5663 13.9479C16.5799 13.9367 16.5919 13.9262 16.6024 13.9166C16.6077 13.9118 16.6126 13.9073 16.6171 13.903C16.6194 13.9008 16.6215 13.8987 16.6236 13.8967C16.6246 13.8957 16.6256 13.8947 16.6266 13.8937C16.6271 13.8932 16.6279 13.8925 16.6281 13.8923C16.6288 13.8916 16.6295 13.8909 16.1007 13.359C15.5719 12.8272 15.5726 12.8265 15.5733 12.8258C15.5735 12.8256 15.5742 12.8249 15.5747 12.8244C15.5756 12.8235 15.5765 12.8226 15.5774 12.8217C15.5793 12.82 15.581 12.8183 15.5827 12.8166C15.5862 12.8133 15.5895 12.8103 15.5926 12.8074C15.5988 12.8018 15.6044 12.7969 15.6094 12.7929C15.6192 12.7847 15.6265 12.7795 15.631 12.7764C15.6403 12.7702 15.6384 12.773 15.6236 12.7785C15.5991 12.7876 15.501 12.8189 15.3038 12.7886C14.8905 12.7253 14.02 12.3853 12.5919 10.9654L11.5343 12.0291ZM9.97365 2.40961C8.95434 1.04802 6.94996 0.83257 5.73263 2.04299L6.79027 3.10667C7.32195 2.578 8.26623 2.63181 8.77286 3.30855L9.97365 2.40961ZM4.75168 5.70305C4.73201 5.35694 4.89075 4.9954 5.22067 4.66736L4.16303 3.60368C3.62571 4.13795 3.20329 4.89425 3.2541 5.78816L4.75168 5.70305ZM19.4723 17.7636C19.1975 18.0369 18.9029 18.1908 18.6067 18.2184L18.7459 19.7119C19.4805 19.6434 20.0824 19.2723 20.5299 18.8273L19.4723 17.7636ZM11.0063 7.72922C11.9908 6.7503 12.064 5.2019 11.2346 4.09401L10.0338 4.99295C10.4373 5.53193 10.3773 6.23938 9.9487 6.66555L11.0063 7.72922ZM20.5251 14.2869C21.3429 14.7315 21.4703 15.7769 20.8929 16.3511L21.9505 17.4148C23.2908 16.0821 22.8775 13.8584 21.2415 12.9691L20.5251 14.2869ZM17.085 13.438C17.469 13.0562 18.0871 12.9616 18.6146 13.2483L19.331 11.9305C18.2474 11.3414 16.9026 11.5041 16.0273 12.3743L17.085 13.438Z" fill="#353535"></path> </g></svg>
        				<span><?=$data['phoneNumber']?></span>
        			</a>
        			
        			<a href="#feedback" class="mini-button fancyboxModal">
			             
    					<span>Заказать звонок</span>
    				</a>

			    </div>
			    <div class="item-inner">
			        <a href="korzina.php" class="header-cart msMiniCart " id="msMiniCart">
    <div class="count ms2_total_count">0</div>
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">

        </g>
        <g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M2.24896 2.29245C1.8582 2.15506 1.43005 2.36047 1.29266 2.75123C1.15527 3.142 1.36068 3.57015 1.75145 3.70754L2.01266 3.79937C2.68026 4.03409 3.11902 4.18964 3.44186 4.34805C3.74509 4.49683 3.87876 4.61726 3.96682 4.74612C4.05708 4.87821 4.12678 5.05963 4.16611 5.42298C4.20726 5.80319 4.20828 6.2984 4.20828 7.03835V9.75999C4.20828 11.2125 4.22191 12.2599 4.35897 13.0601C4.50529 13.9144 4.79742 14.526 5.34366 15.1022C5.93752 15.7285 6.69032 16.0012 7.58656 16.1283C8.44479 16.25 9.53464 16.25 10.8804 16.25L16.2861 16.25C17.0278 16.25 17.6518 16.25 18.1568 16.1882C18.6925 16.1227 19.1811 15.9793 19.6076 15.6318C20.0341 15.2842 20.2731 14.8346 20.4455 14.3232C20.6079 13.841 20.7339 13.2299 20.8836 12.5035L21.3925 10.0341L21.3935 10.0295L21.4039 9.97726C21.5686 9.15237 21.7071 8.45848 21.7416 7.90037C21.7777 7.31417 21.711 6.73616 21.3292 6.23977C21.0942 5.93435 20.7639 5.76144 20.4634 5.65586C20.1569 5.54817 19.8103 5.48587 19.4606 5.44677C18.7735 5.36997 17.9389 5.36998 17.1203 5.36999L5.66809 5.36999C5.6648 5.33324 5.66124 5.29709 5.6574 5.26156C5.60367 4.76518 5.48725 4.31246 5.20527 3.89982C4.92109 3.48396 4.54324 3.21762 4.10261 3.00142C3.69052 2.79922 3.16689 2.61514 2.55036 2.39841L2.24896 2.29245ZM5.70828 6.86999H17.089C17.9454 6.86999 18.6991 6.87099 19.2939 6.93748C19.5895 6.97052 19.8107 7.01642 19.9661 7.07104C20.0931 7.11568 20.1361 7.15213 20.1423 7.1574C20.1422 7.15729 20.1426 7.15762 20.1423 7.1574C20.2037 7.23881 20.2704 7.38651 20.2444 7.80796C20.217 8.25153 20.1005 8.84379 19.9229 9.73372L19.9225 9.73594L19.4237 12.1561C19.2623 12.9389 19.1537 13.4593 19.024 13.8441C18.9009 14.2095 18.7853 14.3669 18.66 14.469C18.5348 14.571 18.3573 14.6525 17.9746 14.6993C17.5714 14.7487 17.0399 14.75 16.2406 14.75H10.9377C9.5209 14.75 8.53783 14.7482 7.79716 14.6432C7.08235 14.5418 6.70473 14.3576 6.43219 14.0701C6.11202 13.7325 5.93933 13.4018 5.83744 12.8069C5.72628 12.1578 5.70828 11.249 5.70828 9.75999L5.70828 6.86999Z" fill="#ffffff">

        </path><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5002 21.75C6.25756 21.75 5.2502 20.7426 5.2502 19.5C5.2502 18.2573 6.25756 17.25 7.5002 17.25C8.74285 17.25 9.7502 18.2573 9.7502 19.5C9.7502 20.7426 8.74285 21.75 7.5002 21.75ZM6.7502 19.5C6.7502 19.9142 7.08599 20.25 7.5002 20.25C7.91442 20.25 8.2502 19.9142 8.2502 19.5C8.2502 19.0858 7.91442 18.75 7.5002 18.75C7.08599 18.75 6.7502 19.0858 6.7502 19.5Z" fill="#ffffff"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M16.5002 21.7501C15.2576 21.7501 14.2502 20.7427 14.2502 19.5001C14.2502 18.2574 15.2576 17.2501 16.5002 17.2501C17.7428 17.2501 18.7502 18.2574 18.7502 19.5001C18.7502 20.7427 17.7428 21.7501 16.5002 21.7501ZM15.7502 19.5001C15.7502 19.9143 16.086 20.2501 16.5002 20.2501C16.9144 20.2501 17.2502 19.9143 17.2502 19.5001C17.2502 19.0859 16.9144 18.7501 16.5002 18.7501C16.086 18.7501 15.7502 19.0859 15.7502 19.5001Z" fill="#ffffff"></path> </g></svg>
</a>

			    </div>
			</div>
			<div class="menu-button">
                <span class="icon-menu-burger">
                    <span class="icon-menu-burger__line"></span>
                </span>
            </div>
		</div>
	</div>
</header> 

<div class="header-push"></div>
        <div class="main">
            
            
                <section class="banners-section">
                    <div class="container">
                        <div class="banners-wrapper swiper-container">
					        <div class="banners swiper-wrapper" id="banners">
                                
<a href="#" class="item swiper-slide">
	<img src="images/banner1.png" alt="">
</a>
<a href="#" class="item swiper-slide">
	<img src="images/banner2.png" alt="">
</a>
<a href="#" class="item swiper-slide">
	<img src="images/banner3.png" alt="">
</a>


                            </div>
                        </div>
                    </div>
                </section>
            
            <div class="container">
                <div class="columns">
                    <div class="aside-column">



    <div class="aside-inner sticky">
    	<div class="aside-menu-title">
            Категории меню
        </div>
        <div class="aside-menu-wrap">
            <ul class="aside-menu">


<div class="sidebar">
    <?php displayCategories($categories); ?>
</div>

            </ul>
        </div>
    </div>
</div>

    <div class="content">
        
<form method="get" action="index.php" class="search-form">
    <input type="text" name="search" id="search" placeholder="Поиск по названию товара">
    <button type="submit">Найти</button>
    <div class="search-results" id="search-results"></div>
</form>

<style>
.product-image {
    width: 200px;
    height: auto;
    border-radius: 10px;
    margin: 5px;
}
</style>

<div id="modal" class="modal" onclick="closeModal(event)">
    <div id="modal-content" class="modal-content" onclick="event.stopPropagation()">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <div id="product-images" class="images"></div>
        <p id="description"></p>
    </div>
</div>

        <div class="catalog">
<?php
// Функция для вывода позиции товара
function displayPosition($position) {
    echo '<div class="position">';
    echo '<div class="images">';
    if (isset($position->images->image[0])) {
        echo '<img src="' . htmlspecialchars($position->images->image[0]) . '" class="image">';
    } else {
        echo '<img src="images/obraz.png" class="image">';
    }
    echo '</div>';
    echo '<h3>' . htmlspecialchars($position->name) . '</h3>';
    echo '<p><strong>Розничная цена:</strong> <span class="price">' . htmlspecialchars($position->price1) . '</span></p>';
    echo '<button class="details-button" onclick="showDetails(' . htmlspecialchars(json_encode($position)) . ')">Подробнее</button>';
    echo '<button class="add-to-cart-button" data-product-id="' . htmlspecialchars($position->id) . '">
    <img src="images/korzina.png" alt="В корзину" />
    </button>';
    echo '</div>';

}

// Поиск по названию товара и номенклатурной группе
$searchQuery = isset($_GET['search']) ? mb_strtolower($_GET['search']) : '';
$searchResults = [];

if (!empty($searchQuery)) {
    foreach ($xml->position as $position) {
        $productName = mb_strtolower((string)$position->name);
        $nomenclatureGroup = mb_strtolower((string)$position->nomenclatureGroup);

        // Проверяем совпадения как по названию товара, так и по номенклатурной группе
        if (stripos($productName, $searchQuery) !== false || stripos($nomenclatureGroup, $searchQuery) !== false) {
            $searchResults[] = $position;
        }
    }
}

if (isset($category) && $category) {
    $subcategories = [];
    $positions = [];

    foreach ($xml->position as $position) {
        if (empty((string) $position->nomenclatureGroupId)) {
            continue;
        }

        $group = trim((string) $position->nomenclatureGroup, '/');
        if (strpos($group, $category) === 0) {
            $relativePath = substr($group, strlen($category) + 1);
            if (strpos($relativePath, '/') === false) {
                $positions[] = $position;
            } else {
                $subcategory = explode('/', $relativePath)[0];
                if ($subcategory && !in_array($subcategory, $subcategories)) {
                    $subcategories[] = $subcategory;
                }
            }
        }
    }

    if (empty($positions) && empty($subcategories)) {
        echo '<h1>Данного товара нет</h1>';
    } else {
        echo '<h1>' . htmlspecialchars($category) . '</h1>';

        if (!empty($subcategories)) {
            echo '<ul class="categories">';
            foreach ($subcategories as $subcategory) {
                echo '<li><a href="index.php?category=' . urlencode($category . '/' . $subcategory) . '">' . htmlspecialchars($subcategory) . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<div class="positions">';
            foreach ($positions as $position) {
                displayPosition($position);
            }
            echo '</div>';
        }
    }
} elseif (!empty($searchQuery)) {
    if (empty($searchResults)) {
        echo '<h1>Данного товара нет</h1>';
    } else {
        echo '<h1>Результаты поиска: ' . htmlspecialchars($searchQuery) . '</h1>';
        echo '<div class="positions">';
        foreach ($searchResults as $position) {
            displayPosition($position);
        }
        echo '</div>';
    }
} else {
    echo '
   <div class="baza">
        <div class="video_container">
            <video autoplay controls>
                <source src="path_to_your_video.mp4" type="video/mp4">
                Ваш браузер не поддерживает элемент <code>video</code>.
            </video>
            <div class="description_text">
                Добро пожаловать в наш магазин электроники! Мы предлагаем широкий выбор продукции от ведущих брендов по выгодным ценам. У нас вы найдете все, что нужно для вашего дома и офиса: от компьютеров и ноутбуков до сетевого оборудования и аксессуаров. Мы доставляем товары по всей России, гарантируя высокое качество и отличное обслуживание.
            </div>
        </div>

        <div class="container_mini">
            <div class="title_mini">Наши преимущества</div>
            <div class="features_mini">
                <div class="feature_mini">
                    <img src="images/wide-choice-icon.png" alt="Широкий выбор">
                    <div class="feature_text">Широкий выбор</div>
                </div>
                <div class="feature_mini">
                    <img src="images/delivery-icon.png" alt="Доставка по всей России">
                    <div class="feature_text">Доставка по всей России</div>
                </div>
                <div class="feature_mini">
                    <img src="images/good-prices-icon.png" alt="Выгодные цены">
                    <div class="feature_text">Выгодные цены</div>
                </div>
            </div>
        </div>

        <div class="container_mini">
            <div class="title_mini">Бренды</div>
            <div class="brands_mini">
                <div class="brand_mini">
                    <img src="images/seagate-logo.png" alt="Seagate">
                    <div class="brand_text">Seagate</div>
                </div>
                <div class="brand_mini">
                    <img src="images/hp-logo.png" alt="HP">
                    <div class="brand_text">HP</div>
                </div>
                <div class="brand_mini">
                    <img src="images/lenovo-logo.png" alt="Lenovo" style="height: 75px; width: 75px; margin-bottom: -5px;">
                    <div class="brand_text">Lenovo</div>
                </div>
                <div class="brand_mini">
                    <img src="images/intel-logo.png" alt="Intel">
                    <div class="brand_text">Intel</div>
                </div>
                <div class="brand_mini">
                    <img src="images/cisco-logo.png" alt="Cisco">
                    <div class="brand_text">Cisco</div>
                </div>
            </div>
        </div>
    </div>

';
}
?>
<style>
  .baza {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .container_mini {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 20px;
            width: 90%;
            max-width: 900px;
            text-align: center;
        }

        .title_mini {
            text-align: center;
            font-size: 28px;
            color: #1E12A5;
            margin-bottom: 25px;
            font-weight: 550;
        }

        .features_mini, .brands_mini {
            display: flex;
            justify-content: space-evenly;
            flex-wrap: wrap;
            gap: 20px;
        }

        .feature_mini, .brand_mini {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex: 1 1 150px;
            max-width: 200px;
            border: 4px solid #1E12A5;
        }

        .feature_mini img, .brand_mini img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        .feature_text, .brand_text {
            font-size: 18px;
            color: #242424;
        }

        .video_container {
            width: 90%;
            max-width: 900px;
            margin: 20px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .video_container video {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .description_text {
            font-size: 20px;
            color: #242424;
            margin-top: 20px;
        }
</style>



<script>
function showDetails(position) {
    // Создаем содержимое для модального окна
    var description = `
        <p>
            <strong>Количество:</strong> ${escapeHtml(position.count)}<br>
            <strong>Розничная цена:</strong> <span class="price">${escapeHtml(position.price1)}</span><br>
            <strong>Оптовая цена:</strong> <span class="price">${escapeHtml(position.price2)}</span><br>
            <strong>Крупный опт:</strong> <span class="price">${escapeHtml(position.price3)}</span>
        </p>
    `;

    // Вставляем содержимое в модальное окно
    document.getElementById('description').innerHTML = description;

    // Устанавливаем изображения продукта в модальном окне
    var imagesHtml = '';
    if (position.images && position.images.image) {
        var images = Array.isArray(position.images.image) ? position.images.image : [position.images.image];
        images.forEach(function(imageUrl) {
            imagesHtml += `<img src="${escapeHtml(imageUrl)}" alt="Product Image" class="product-image">`;
        });
    } else {
        imagesHtml = '<img src="images/obraz.png" alt="Product Image" class="product-image">';
    }
    document.getElementById('product-images').innerHTML = imagesHtml;

    // Показываем модальное окно
    document.getElementById('modal').style.display = 'block';
}

function closeModal(event) {
    // Закрываем модальное окно, если клик был по фону
    if (event.target == document.getElementById('modal') || event.target.className == 'close') {
        document.getElementById('modal').style.display = 'none';
    }
}
function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

// Функция для экранирования HTML символов
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>



        </div>
    </div>



    <script src="js/display_script.js"></script>
<div class="pagination"><ul></ul></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="footer-section-wrapper">
        </div>
    </section>
    <footer class="footer-wrapper"> 
    
      <div id="contact"></div>
		<div id="popup-1" style="display: none">
            <p>
                Здесь можно разместить все, что угодно - любую <b>текстовую информацию</b> или нужный Вам <b>html код</b>. Например, Ваши реквизиты или другие сведения о компании.<br>Либо можно указать информацию про акции, бонусы, скидки. А также какую-нибудь картинку или видео.<br>Кроме того, здесь можно разместить например, яндекс или гугл карту местонахождения офиса.
            </p>
        </div>
    
        <div class="container">
            <div class="footer">
                <div class="left-part">
                    <div class="item top">
                        <div class="logo-wrapper">
            				

            	            
            			</div>
                        <div class="footer-menu">
                            <ul class="menu-nav">
                                <li class="first">   
                                     <a href="index.php" data-hover="Главная">Главная</a>
                                </li>
                                <li> 
                                    <a href="#about" data-hover="Доставка и оплата">Доставка и оплата</a>
                                </li>
                                <li>
                                    <a href="korzina.php" data-hover="Корзина">Корзина</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div class="item bottom">
                        <div class="contacts-wrapper">
    
    <div class="title-main text-left">
        Контакты
    </div>
    
    <div class="contacts">
        
            <div class="item-in">
                <div>Телефон:</div>
                <a class="value" href="tel:<?=$data['phoneNumber']?>"><?=$data['phoneNumber']?></a>
            </div>
        
        
            <div class="item-in">
                <div>Почта:</div>
                <a class="value" href="mailto:<?=$data['sendMail']?>"><?=$data['sendMail']?></a>
            </div>

            <div class="item-in">
                <div>Часы работы:</div>
                <div class="value"><?=$data['grafic']?></div>
            </div>
        
    </div>
</div>
                        <div class="socials-wrapper">
        <div class="socials socials-mini">
            
		    	<a class="item-in whatsapp" href="https://api.whatsapp.com/send/?phone=70000000000">
					<div class="icon">
					    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.3886 0.0127276C9.32586 0.13347 8.68339 0.262118 7.91223 0.508673C4.54559 1.58495 1.91113 4.19522 0.805115 7.55039C0.447744 8.63454 0.30049 9.47561 0.26358 10.6441C0.190791 12.9459 0.807951 15.1326 2.06096 17.0126C2.19816 17.2185 2.31044 17.4121 2.31044 17.4428C2.31044 17.4736 2.01108 18.3963 1.6452 19.4934C1.27932 20.5905 0.986185 21.4944 0.993834 21.502C1.00148 21.5097 1.95028 21.2145 3.10227 20.846L5.19674 20.1761L5.54753 20.3898C7.95928 21.8583 10.8375 22.3335 13.6416 21.726C16.9828 21.0021 19.8604 18.6416 21.3106 15.4352C21.6942 14.587 22.0458 13.3203 22.1575 12.3844C22.1828 12.1717 22.2159 11.9203 22.2309 11.8258C22.2824 11.5004 22.298 10.205 22.2499 10.2347C22.2211 10.2525 22.205 10.1919 22.205 10.0663C22.205 9.5395 21.9742 8.41028 21.6908 7.55039C21.1341 5.86112 20.2548 4.44564 18.9755 3.17931C17.2712 1.49227 15.2505 0.481216 12.8087 0.0938525C12.4078 0.0302588 10.7295 -0.0260303 10.3886 0.0127276ZM6.74554 5.06022C6.51815 5.10096 6.2337 5.22424 6.0675 5.35413C6.01873 5.39224 5.86774 5.54796 5.732 5.7002C4.84186 6.69828 4.62946 8.16304 5.16812 9.58909C5.63687 10.83 6.92275 12.6125 8.33144 13.9742C9.59012 15.1909 10.746 15.9209 12.374 16.5273C13.6071 16.9865 14.4122 17.1635 15.0681 17.1195C16.0291 17.0551 17.1995 16.3904 17.5424 15.7145C17.7853 15.2357 17.9509 14.2363 17.8261 14.003C17.7748 13.9071 16.5068 13.2479 15.5007 12.794C14.6142 12.394 14.5246 12.4213 13.9154 13.2768C13.7678 13.4841 13.5541 13.7548 13.4405 13.8784L13.2338 14.1031H12.9176C12.6176 14.1031 12.5734 14.0895 12.052 13.8362C11.1742 13.4099 10.5105 12.9481 9.81407 12.2794C9.10659 11.6001 8.15389 10.2894 8.15436 9.99613C8.15458 9.85227 8.26049 9.699 8.68515 9.22789C8.88672 9.00432 9.10337 8.71906 9.16666 8.59397C9.26601 8.39769 9.27597 8.34445 9.23967 8.20554C9.17719 7.96672 8.15552 5.5352 8.03951 5.34932C7.98459 5.26132 7.89466 5.15983 7.83974 5.12382C7.71458 5.04183 7.06068 5.00381 6.74554 5.06022ZM0.265513 10.9664C0.26547 11.3682 0.271916 11.5383 0.279865 11.3445C0.287814 11.1507 0.287857 10.822 0.279951 10.614C0.272088 10.4061 0.265556 10.5646 0.265513 10.9664Z" fill="white"></path>
                        </svg>
					</div>
				</a>
		    
		     
		    	<a class="item-in tg" href="tg://resolve?domain=username">
					<div class="icon">
					    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22" fill="none">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1988 0.02757C8.84105 0.168679 7.55612 0.539285 6.37426 1.13066C2.47476 3.08187 0.103271 7.05644 0.26565 11.3687C0.386092 14.5682 1.8154 17.4451 4.31159 19.5123C5.49735 20.4943 7.25039 21.3518 8.78034 21.6982C9.70967 21.9086 10.1721 21.9571 11.251 21.9571C12.3217 21.9571 12.7858 21.9091 13.7003 21.7039C16.409 21.0961 18.8345 19.4183 20.4108 17.062C21.3885 15.6006 22.078 13.651 22.1927 12.0239C22.2082 11.8053 22.2372 11.6265 22.2574 11.6265C22.2775 11.6265 22.2937 11.3123 22.2934 10.9282C22.2929 10.4615 22.2789 10.2513 22.251 10.2944C22.2213 10.3405 22.2092 10.2929 22.2087 10.1281C22.2071 9.54412 21.9662 8.35311 21.6693 7.46121C21.4319 6.7478 20.8617 5.57707 20.4485 4.95471C18.6787 2.2888 15.9915 0.580062 12.7979 0.0898747C12.2575 0.00690207 10.7462 -0.0293206 10.1988 0.02757ZM15.9346 6.64102C15.8756 6.66001 14.9668 7.00814 13.9151 7.41467C12.8634 7.82116 11.3842 8.39221 10.628 8.68367C5.99304 10.4698 5.20272 10.7788 5.04867 10.8649C4.80341 11.002 4.69216 11.1654 4.74394 11.3126C4.76637 11.3764 4.84281 11.4625 4.91384 11.504C5.09383 11.6091 7.81023 12.4539 7.89488 12.4311C7.93287 12.4208 9.38512 11.5172 11.1221 10.423C13.6052 8.85881 14.3115 8.4326 14.4263 8.42938C14.7438 8.42048 14.9824 8.18918 10.4669 12.2663C9.60113 13.048 9.40339 13.2471 9.40339 13.3372C9.40339 13.3981 9.36304 14.0161 9.31371 14.7106C9.26442 15.405 9.23405 15.9832 9.2462 15.9953C9.25836 16.0075 9.33171 16.0035 9.40919 15.9865C9.51222 15.9639 9.75934 15.7558 10.3294 15.2117L11.1088 14.4677L12.4797 15.4801C13.2337 16.0369 13.9129 16.5191 13.9889 16.5516C14.3018 16.6856 14.6082 16.59 14.751 16.314C14.85 16.1224 16.6651 7.50516 16.6651 7.22643C16.6651 6.9213 16.5752 6.73993 16.3843 6.66018C16.2244 6.59337 16.0995 6.58804 15.9346 6.64102ZM0.268529 10.9819C0.268529 11.3719 0.27506 11.5314 0.28301 11.3364C0.290959 11.1414 0.290959 10.8224 0.28301 10.6274C0.27506 10.4325 0.268529 10.592 0.268529 10.9819Z" fill="white"></path>
                        </svg>
					</div>
				</a>
		    
		     

		    
        </div>
     </div>
                        <a class="confident" href="politica.html">Политика конфиденциальности</a>
                        <div class="development">Разработано в <a href="https://ensoez.ru/" target="_blank">ensoez.ru</a></div>
                        <div class="push20"></div>
                    </div>
                </div>
                <div class="right-part">
                    <div class="footer-form">
                        <div class="title-main">Обратная связь</div>
                        <div class="rf">
<form action ="" method="POST" id="feedback-valid" ipp="<?=$data['notificationMethod']?>" class="ajax_form">
	<div class="form-grid">
    	<div class="form-group">
    	    <input type="hidden" name="source" value="connect">
    		<input name="user_name" value="" type="text" class="form-control required" placeholder="Как вас зовут? *">
    	</div>
    	<div class="form-group">
    		<input name="user_phone" value="" type="text" class="form-control required tel" placeholder="Ваш телефон *">
    	</div>
	</div>
    <div class="form-text">
        Оставляя заявку, вы даете согласие на <a href="politica.html" target="_blank">обработку персональных данные</a>
    </div>
	<div class="text-center">
		<input type="submit" class="button" value="Оставить заявку" id="submit-button">
	</div>
</form>
                		</div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

<div class="about-modal" id="about">
    <div class="close">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="19" viewBox="0 0 20 19" fill="none">
            <path d="M17.1015 0.920938C17.5633 0.459096 18.3121 0.459095 18.774 0.920938C19.2358 1.38278 19.2358 2.13158 18.774 2.59342L2.76878 18.5986C2.30693 19.0605 1.55814 19.0605 1.0963 18.5986C0.634453 18.1368 0.634453 17.388 1.0963 16.9261L17.1015 0.920938Z" fill="black"></path>
            <path d="M17.1015 0.920938C17.5633 0.459096 18.3121 0.459095 18.774 0.920938C19.2358 1.38278 19.2358 2.13158 18.774 2.59342L2.76878 18.5986C2.30693 19.0605 1.55814 19.0605 1.0963 18.5986C0.634453 18.1368 0.634453 17.388 1.0963 16.9261L17.1015 0.920938Z" fill="black"></path>
            <path d="M19.1078 16.9251C19.5697 17.387 19.5697 18.1358 19.1078 18.5976C18.646 19.0595 17.8972 19.0595 17.4353 18.5976L1.43014 2.59244C0.968297 2.1306 0.968297 1.3818 1.43014 0.919958C1.89198 0.458115 2.64078 0.458115 3.10262 0.919958L19.1078 16.9251Z" fill="black"></path>
            <path d="M19.1078 16.9251C19.5697 17.387 19.5697 18.1358 19.1078 18.5976C18.646 19.0595 17.8972 19.0595 17.4353 18.5976L1.43014 2.59244C0.968297 2.1306 0.968297 1.3818 1.43014 0.919958C1.89198 0.458115 2.64078 0.458115 3.10262 0.919958L19.1078 16.9251Z" fill="black"></path>
        </svg>
    </div>
    <h1 class="pagetitle">Доствака и оплата</h1>
    <div class="content">Внезапно, действия представителей оппозиции, инициированные исключительно синтетически, описаны максимально подробно. Ясность нашей позиции очевидна: начало повседневной работы по формированию позиции обеспечивает актуальность направлений прогрессивного развития. Высокий уровень вовлечения представителей целевой аудитории является четким доказательством простого факта: убеждённость некоторых оппонентов предоставляет широкие возможности для экономической целесообразности принимаемых решений. В рамках спецификации современных стандартов, предприниматели в сети интернет и по сей день остаются уделом либералов, которые жаждут быть преданы социально-демократической анафеме. Идейные соображения высшего порядка, а также убеждённость некоторых оппонентов не даёт нам иного выбора, кроме определения новых предложений!</div>
    <div class="contacts-wrapper">
    
    <div class="title-main text-left">
        Контакты
    </div>
    
    <div class="contacts">
        
            <div class="item-in">
                <div>Телефон:</div>
                <a class="value" href="tel:<?=$data['phoneNumber']?>"><?=$data['phoneNumber']?></a>
            </div>
        
        
            <div class="item-in">
                <div>Почта:</div>
                <a class="value" href="mailto:<?=$data['sendMail']?>"><?=$data['sendMail']?></a>
            </div>
        
            <div class="item-in">
                <div>Часы работы:</div>
                <div class="value"><?=$data['grafic']?></div>
            </div>
        
    </div>
</div>
    <div class="socials-wrapper">
        <div class="socials socials-mini">
            
		    	<a class="item-in whatsapp" href="https://api.whatsapp.com/send/?phone=70000000000">
					<div class="icon">
					    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.3886 0.0127276C9.32586 0.13347 8.68339 0.262118 7.91223 0.508673C4.54559 1.58495 1.91113 4.19522 0.805115 7.55039C0.447744 8.63454 0.30049 9.47561 0.26358 10.6441C0.190791 12.9459 0.807951 15.1326 2.06096 17.0126C2.19816 17.2185 2.31044 17.4121 2.31044 17.4428C2.31044 17.4736 2.01108 18.3963 1.6452 19.4934C1.27932 20.5905 0.986185 21.4944 0.993834 21.502C1.00148 21.5097 1.95028 21.2145 3.10227 20.846L5.19674 20.1761L5.54753 20.3898C7.95928 21.8583 10.8375 22.3335 13.6416 21.726C16.9828 21.0021 19.8604 18.6416 21.3106 15.4352C21.6942 14.587 22.0458 13.3203 22.1575 12.3844C22.1828 12.1717 22.2159 11.9203 22.2309 11.8258C22.2824 11.5004 22.298 10.205 22.2499 10.2347C22.2211 10.2525 22.205 10.1919 22.205 10.0663C22.205 9.5395 21.9742 8.41028 21.6908 7.55039C21.1341 5.86112 20.2548 4.44564 18.9755 3.17931C17.2712 1.49227 15.2505 0.481216 12.8087 0.0938525C12.4078 0.0302588 10.7295 -0.0260303 10.3886 0.0127276ZM6.74554 5.06022C6.51815 5.10096 6.2337 5.22424 6.0675 5.35413C6.01873 5.39224 5.86774 5.54796 5.732 5.7002C4.84186 6.69828 4.62946 8.16304 5.16812 9.58909C5.63687 10.83 6.92275 12.6125 8.33144 13.9742C9.59012 15.1909 10.746 15.9209 12.374 16.5273C13.6071 16.9865 14.4122 17.1635 15.0681 17.1195C16.0291 17.0551 17.1995 16.3904 17.5424 15.7145C17.7853 15.2357 17.9509 14.2363 17.8261 14.003C17.7748 13.9071 16.5068 13.2479 15.5007 12.794C14.6142 12.394 14.5246 12.4213 13.9154 13.2768C13.7678 13.4841 13.5541 13.7548 13.4405 13.8784L13.2338 14.1031H12.9176C12.6176 14.1031 12.5734 14.0895 12.052 13.8362C11.1742 13.4099 10.5105 12.9481 9.81407 12.2794C9.10659 11.6001 8.15389 10.2894 8.15436 9.99613C8.15458 9.85227 8.26049 9.699 8.68515 9.22789C8.88672 9.00432 9.10337 8.71906 9.16666 8.59397C9.26601 8.39769 9.27597 8.34445 9.23967 8.20554C9.17719 7.96672 8.15552 5.5352 8.03951 5.34932C7.98459 5.26132 7.89466 5.15983 7.83974 5.12382C7.71458 5.04183 7.06068 5.00381 6.74554 5.06022ZM0.265513 10.9664C0.26547 11.3682 0.271916 11.5383 0.279865 11.3445C0.287814 11.1507 0.287857 10.822 0.279951 10.614C0.272088 10.4061 0.265556 10.5646 0.265513 10.9664Z" fill="white"></path>
                        </svg>
					</div>
				</a>
		    
		     
		    	<a class="item-in tg" href="tg://resolve?domain=username">
					<div class="icon">
					    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22" fill="none">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1988 0.02757C8.84105 0.168679 7.55612 0.539285 6.37426 1.13066C2.47476 3.08187 0.103271 7.05644 0.26565 11.3687C0.386092 14.5682 1.8154 17.4451 4.31159 19.5123C5.49735 20.4943 7.25039 21.3518 8.78034 21.6982C9.70967 21.9086 10.1721 21.9571 11.251 21.9571C12.3217 21.9571 12.7858 21.9091 13.7003 21.7039C16.409 21.0961 18.8345 19.4183 20.4108 17.062C21.3885 15.6006 22.078 13.651 22.1927 12.0239C22.2082 11.8053 22.2372 11.6265 22.2574 11.6265C22.2775 11.6265 22.2937 11.3123 22.2934 10.9282C22.2929 10.4615 22.2789 10.2513 22.251 10.2944C22.2213 10.3405 22.2092 10.2929 22.2087 10.1281C22.2071 9.54412 21.9662 8.35311 21.6693 7.46121C21.4319 6.7478 20.8617 5.57707 20.4485 4.95471C18.6787 2.2888 15.9915 0.580062 12.7979 0.0898747C12.2575 0.00690207 10.7462 -0.0293206 10.1988 0.02757ZM15.9346 6.64102C15.8756 6.66001 14.9668 7.00814 13.9151 7.41467C12.8634 7.82116 11.3842 8.39221 10.628 8.68367C5.99304 10.4698 5.20272 10.7788 5.04867 10.8649C4.80341 11.002 4.69216 11.1654 4.74394 11.3126C4.76637 11.3764 4.84281 11.4625 4.91384 11.504C5.09383 11.6091 7.81023 12.4539 7.89488 12.4311C7.93287 12.4208 9.38512 11.5172 11.1221 10.423C13.6052 8.85881 14.3115 8.4326 14.4263 8.42938C14.7438 8.42048 14.9824 8.18918 10.4669 12.2663C9.60113 13.048 9.40339 13.2471 9.40339 13.3372C9.40339 13.3981 9.36304 14.0161 9.31371 14.7106C9.26442 15.405 9.23405 15.9832 9.2462 15.9953C9.25836 16.0075 9.33171 16.0035 9.40919 15.9865C9.51222 15.9639 9.75934 15.7558 10.3294 15.2117L11.1088 14.4677L12.4797 15.4801C13.2337 16.0369 13.9129 16.5191 13.9889 16.5516C14.3018 16.6856 14.6082 16.59 14.751 16.314C14.85 16.1224 16.6651 7.50516 16.6651 7.22643C16.6651 6.9213 16.5752 6.73993 16.3843 6.66018C16.2244 6.59337 16.0995 6.58804 15.9346 6.64102ZM0.268529 10.9819C0.268529 11.3719 0.27506 11.5314 0.28301 11.3364C0.290959 11.1414 0.290959 10.8224 0.28301 10.6274C0.27506 10.4325 0.268529 10.592 0.268529 10.9819Z" fill="white"></path>
                        </svg>
					</div>
				</a>
		    
        </div>
     </div>
</div>

<div class="overlay-about"></div>
<div class="fancybox_modal" id="feedback">
	<div class="title">Заказать звонок</div>
	<div class="rf">
	    <form action ="" method="POST" id="feedback-valid2" ipp="<?=$data['notificationMethod']?>" class="ajax_form">
	<div class="form-grid">
    	<div class="form-group">
    	    <input type="hidden" name="source" value="connect">
    		<input type="text" name="user_name" class="form-control required" placeholder="Как вас зовут? *">
    	</div>
    	<div class="form-group">
    		<input name="user_phone" type="text" class="form-control required tel" placeholder="Ваш телефон *">
    	</div>
	</div>
    <div class="form-text">
        Оставляя заявку, вы даете согласие на <a href="politica.html" target="_blank">обработку персональных данные</a>
    </div>
	<div class="text-center">
		<input type="submit" class="button" value="Оставить заявку" id="submit-button">
	</div>
</form>
	</div> 


<script src="js/jquery.fancybox3.min.js"></script>
<script src="js/swiper-bundle.min.js"></script>
<script src="js/jquery.inputmask.js"></script>
<script src="js/jquery.formstyler.js"></script>
<script src="js/scripts.js"></script>

 <div class="modal" id="responseMessage">
    <div class="title" id="responseMessageTitle"></div>
    <hr>
    <div class="modal-body" id="responseMessageBody"></div>
    <a href="#" class="button fancyClose">Закрыть</a>
</div>

<script>
     $(document).on('af_complete', function(event, response) {
        $('body').addClass('jgrowlHide');
            setTimeout(function() {
            $('body').removeClass('jgrowlHide');
        }, 5000);
        
        
        if(response.success){
            $.fancybox.open($('#responseMessage'));
            $('#responseMessageTitle').text('Сообщение успешно отправлено!');
            $('#responseMessageBody').html('<p>'+response.message+'</p>');
            $('.fancyClose').click(function(){
                $.fancybox.close('#responseMessage');
                return false;
            });
        }else{
            $.fancybox.open($('#responseMessage'));
            $('#responseMessageTitle').text('Сообщение не отправлено!');
            $('#responseMessageBody').html('<p>'+response.message+'</p>');
            $('.fancyClose').click(function(){
                $.fancybox.close('#responseMessage');
                return false;
            });
        }
    });
    $(function(){
</script>
<script>
        var numberOfSlidesLoaded = document.querySelectorAll('.banners .swiper-slide').length;
        if (numberOfSlidesLoaded < 3) { 
            var element = document.getElementById('banners');
            element.style.justifyContent = 'center';
        }
        var banners = new Swiper('.banners-wrapper',{
            slidesPerView: 1,
            spaceBetween: 25,  
            breakpoints: {
                991: {
                    spaceBetween: 25,
                    slidesPerView: 3,
                },
                555: {
                    spaceBetween: 25,
                    slidesPerView: 2,
                },
            },
        });
        
        /*aside*/

        let headerHeight = $('.header-inner').innerHeight() + 18,
        panel2=$('.aside-inner'),
        posTop=panel2.offset().top,
        posBottomEnd = posTop + $('.main-column').height() - 100;

        let panelHeight = panel2.height() + 120 + 20;  
        if ( ((panelHeight + headerHeight) > $(window).height()) && !panel2.hasClass('over') ){
            panel2.addClass('over');
        }
        if ( ((panelHeight - 120 + headerHeight) <= $(window).height()) && panel2.hasClass('over') ) {
            panel2.removeClass('over');
        }
        window.addEventListener("resize", function() {
            if ( ((panelHeight + headerHeight) > $(window).height()) && !panel2.hasClass('over') ){
                panel2.addClass('over');
            }
            if ( ((panelHeight - 120 + headerHeight) <= $(window).height()) && panel2.hasClass('over') ) {
                panel2.removeClass('over');
            }
        });
    </script>
<script>
$(document).ready(function(){
    // Обработка отправки формы
    function handleFormSubmit(formId) {
        $('#' + formId).submit(function(e){
            e.preventDefault(); // Предотвращаем стандартное действие отправки формы

            // Получаем значение атрибута ipp
            var formAction = $(this).attr('ipp');

            // Получаем данные формы
            var formData = $(this).serialize();

            // Получаем товары из корзины (должен быть массив с ID товаров)
            var cart = <?php echo json_encode($cart); ?>;

            // Используем XMLHttpRequest для обращения к PHP-файлу, который выполнит поиск названий товаров по их ID
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'server/get_product_names.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var productNames = JSON.parse(xhr.responseText);
                    formData += '&user_products=' + encodeURIComponent(JSON.stringify(productNames));

                    // Функция для отображения модального окна благодарности
                    function showThanksModal() {
                        $('#thanks').modal('show');
                    }

                    if (formAction === 'pochta') {
                        $.ajax({
                            type: 'POST',
                            url: 'server/mail.php',
                            data: formData,
                            success: function(response) {
                                if (response.status === 'success') {
                                    console.log('Успешная отправка на mail.php. Ответ:', response);
                                    showThanksModal();
                                } else {
                                    // Обработка ошибки, если необходимо
                                }
                            },
                            error: function(error) {
                                console.error('Ошибка при отправке на mail.php:', error);
                            }
                        });
                    } else if (formAction === 'telega') {
                        $.ajax({
                            type: 'POST',
                            url: 'server/index.php',
                            data: formData,
                            success: function(response) {
                                if (response.status === 'success') {
                                    console.log('Успешная отправка на server/index.php. Ответ:', response);
                                    showThanksModal();
                                } else {
                                    // Обработка ошибки, если необходимо
                                }
                            },
                            error: function(error) {
                                console.error('Ошибка при отправке на server/index.php:', error);
                            }
                        });
                    } else if (formAction === 'telegaPochta') {
                        $.ajax({
                            type: 'POST',
                            url: 'server/mail.php',
                            data: formData,
                            success: function(response) {
                                if (response.status === 'success') {
                                    console.log('Успешная отправка на mail.php. Ответ:', response);
                                    showThanksModal();
                                } else {
                                    // Обработка ошибки, если необходимо
                                }
                            },
                            error: function(error) {
                                console.error('Ошибка при отправке на mail.php:', error);
                            }
                        });

                        $.ajax({
                            type: 'POST',
                            url: 'server/index.php',
                            data: formData,
                            success: function(response) {
                                if (response.status === 'success') {
                                    console.log('Успешная отправка на server/index.php. Ответ:', response);
                                    showThanksModal();
                                } else {
                                    // Обработка ошибки, если необходимо
                                }
                            },
                            error: function(error) {
                                console.error('Ошибка при отправке на server/index.php:', error);
                            }
                        });
                    }
                }
            };
            xhr.send('cart=' + encodeURIComponent(JSON.stringify(cart)));
        });
    }

    // Привязываем обработчики к формам
    handleFormSubmit('feedback-valid');
    handleFormSubmit('feedback-valid2');

    // Обработка нажатия на кнопку заказа
    document.getElementById('orderButton').addEventListener('click', function() {
        openOrderForm();
    });
});
</script>

</body>
</html>