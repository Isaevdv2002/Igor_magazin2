

function closeOrderForm() {
    document.getElementById('orderForm').style.display = 'none';
}

function showPopup(message) {
    var popup = document.getElementById("addToCartPopup");
    popup.textContent = message;
    popup.style.display = "block";

    // Скрывайте всплывающее окно через 2 секунды (или другой необходимый интервал)
    setTimeout(function() {
        popup.style.display = "none";
    }, 1500);
}







function getCartFromCookies() {
    var name = "cart=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookies = decodedCookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        if (cookie.indexOf(name) == 0) {
            var cartValue = cookie.substring(name.length, cookie.length);
            if (cartValue) {
                return cartValue; // Возвращаем значение, если оно не пустое
            }
        }
    }
    return "[]"; // Возвращаем пустой массив, если корзина пуста
}

document.addEventListener('DOMContentLoaded', function() {
    function handleFormSubmission(event) {
        event.preventDefault(); // Предотвращаем отправку формы по умолчанию

        // Показываем надпись "Принято" при помощи CSS
        var confirmationMessage = document.getElementById('confirmation-message');
        confirmationMessage.style.display = 'block';

        // Скрываем надпись "Принято" через 1.5 секунды
        setTimeout(function() {
            confirmationMessage.style.display = 'none';
        }, 1700); // 1500 миллисекунд = 1.5 секунды

        // Закрываем форму через 5 секунд
        setTimeout(function() {
            var closeOrderForm = document.querySelector('.fancybox-button[data-fancybox-close]');
            if (closeOrderForm) {
                closeOrderForm.click(); // Имитируем нажатие на кнопку закрытия формы
            } else {
                // Альтернативное закрытие формы, если кнопка не найдена
                document.getElementById('orderForm').style.display = 'none';
            }
        }, 5); // 5000 миллисекунд = 5 секунд
    }

    document.getElementById('feedback-valid').addEventListener('submit', handleFormSubmission);
    document.getElementById('feedback-valid2').addEventListener('submit', handleFormSubmission);
});





document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('search');
    var searchResults = document.getElementById('search-results');

    searchInput.addEventListener('input', function() {
        var query = this.value.trim();

        // Проверяем, достаточно ли символов для поиска
        if (query.length < 2) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none'; // Скрываем подсказки, если символов недостаточно
            return;
        }

        // Отправляем запрос на сервер для получения подсказок
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Обновляем список подсказок
                    var suggestions = JSON.parse(xhr.responseText);
                    renderSuggestions(suggestions);
                } else {
                    console.error('Ошибка при получении подсказок:', xhr.status);
                }
            }
        };
        xhr.open('GET', 'https://westcargogroupe.ru/server/suggest.php?query=' + encodeURIComponent(query), true);
        xhr.send();
    });

function renderSuggestions(suggestions) {
    var html = '<ul>'; // Изменяем обертку для списка результатов на <ul>
    suggestions.forEach(function(suggestion) {
        html += '<li class="suggestion" onclick="selectSuggestion(\'' + suggestion + '\')">' + suggestion + '</li>'; // Изменяем div на li
    });
    html += '</ul>'; // Закрываем <ul>
    searchResults.innerHTML = html;
    searchResults.style.display = 'block'; // Показываем подсказки после получения данных
}

});

// Функция для выбора подсказки
function selectSuggestion(suggestion) {
    var searchInput = document.getElementById('search');
    searchInput.value = suggestion; // Устанавливаем выбранную подсказку в поле ввода
    var searchResults = document.getElementById('search-results');
    searchResults.style.display = 'none'; // Скрываем подсказки после выбора
}

// Закрывать подсказки при клике вне поля ввода или подсказок
document.addEventListener('click', function(event) {
    var searchInput = document.getElementById('search');
    var searchResults = document.getElementById('search-results');

    if (event.target !== searchInput && event.target !== searchResults) {
        searchResults.style.display = 'none';
    }
});
