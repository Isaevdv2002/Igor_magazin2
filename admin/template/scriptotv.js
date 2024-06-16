var currentUrl = window.location.href;
var urlWithoutHash = currentUrl.split('#')[0];
window.history.replaceState({}, document.title, urlWithoutHash);
console.log('Hide notification function called');

document.addEventListener('DOMContentLoaded', function () {
    const telegramFields = document.getElementById('telegramFields');
    const emailFields = document.getElementById('emailFields');
    const telegramRadio = document.getElementById('telegramOnly');
    const emailRadio = document.getElementById('emailOnly');
    const telegramAndEmailRadio = document.getElementById('telegramAndEmail');
    const emailLabel = document.querySelector('.email-label');
    const statFields = document.querySelector('.stat-fields');


    const showTelegramFields = function () {
        telegramFields.style.display = telegramRadio.checked ? 'block' : 'none';
        emailFields.style.display = 'none';
    };

    const showEmailFields = function () {
        emailFields.style.display = emailRadio.checked ? 'block' : 'none';
        telegramFields.style.display = 'none';
    };

    const showTelegramAndEmailFields = function () {
        telegramFields.style.display = telegramAndEmailRadio.checked ? 'block' : 'none';
        emailFields.style.display = telegramAndEmailRadio.checked ? 'block' : 'none';
    };

    const hideFormFields = function () {
        telegramFields.style.display = 'none';
        emailFields.style.display = 'none';
    };

    const searchInput = document.getElementById('searchInput');
    const adminPanel = document.querySelector('.admin-panel');
    const searchButton = document.getElementById('searchButton');
    const contactFields = document.querySelector('.contact-fields');
    const textFields = document.querySelector('.text-fields');
    const priceListFields = document.querySelector('.price-list-fields');
    const categorySelectLabel = document.querySelector('.category-select-label');
    const searchContainer = document.getElementById('searchContainer');
    const instructionButton = document.getElementById('instructionButton');
    const instructionContent = document.getElementById('instructionContent');

    const scrollToFirstMatchedElement = function () {
        const searchText = searchInput.value.toLowerCase();
        const inputs = document.querySelectorAll('li input[type="text"]');
        let firstMatchedElement = null;

        inputs.forEach(function (input) {
            const inputValue = input.value.toLowerCase();
            const label = input.closest('li').querySelector('label').innerText.toLowerCase();

            if (inputValue.includes(searchText) || label.includes(searchText)) {
                input.classList.add('highlight');
                if (!firstMatchedElement) {
                    firstMatchedElement = input;
                }
            } else {
                input.classList.remove('highlight');
            }
        });

        if (firstMatchedElement) {
            const rect = firstMatchedElement.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const centerOffset = (windowHeight - rect.height) / 2;

            window.scrollTo({
                top: rect.top - centerOffset + window.scrollY,
                behavior: 'smooth'
            });

            setTimeout(function () {
                firstMatchedElement.classList.remove('highlight');
            }, 2000);
        }
    };


    const showContactFields = function () {
        contactFields.style.display = 'block';
        priceListFields.style.display = 'none';
        categorySelectLabel.style.display = 'none';
        textFields.style.display = 'none';
        searchContainer.style.display = 'none';
        statFields.style.display = 'none';
        hideFormFields();
    };



    const showTextFields = function () {
        textFields.style.display = 'block';
        contactFields.style.display = 'none';
        priceListFields.style.display = 'none';
        categorySelectLabel.style.display = 'none';
        searchContainer.style.display = 'none';
        statFields.style.display = 'none';
        hideFormFields();
    };

    const toggleInstructionContent = function (event) {
        event.preventDefault();
        instructionContent.style.display = (instructionContent.style.display === 'block') ? 'none' : 'block';
    };

    searchButton.addEventListener('click', scrollToFirstMatchedElement);
    document.getElementById('contactBtn').addEventListener('click', showContactFields);
    
    document.getElementById('textBtn').addEventListener('click', showTextFields);
    telegramRadio.addEventListener('click', showTelegramFields);
    emailRadio.addEventListener('click', showEmailFields);
    telegramAndEmailRadio.addEventListener('click', showTelegramAndEmailFields);
    instructionButton.addEventListener('click', toggleInstructionContent);

    const openModal = function (imageName) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modal.style.display = 'block';
        modalImage.src = `template/${imageName}`;

        setTimeout(() => {
            modalImage.parentElement.classList.add('show');
        }, 50);

        setTimeout(() => {
            modal.classList.add('show');
        }, 100);
    };

    const closeModal = function () {
        const modal = document.getElementById('imageModal');
        modal.classList.remove('show');
        modal.querySelector('.modal-content').classList.remove('show');

        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    };


    document.addEventListener('DOMContentLoaded', function () {
        var selectedRadios = JSON.parse(localStorage.getItem('selectedRadios')) || {};

        function setupRadioButtons(name) {
            document.querySelectorAll('input[name="' + name + '"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    var value = this.value;
                    selectedRadios[name] = value;
                    localStorage.setItem('selectedRadios', JSON.stringify(selectedRadios));
                });
            });
        }

        Object.keys(selectedRadios).forEach(function (name) {
            var value = selectedRadios[name];
            document.querySelector('input[name="' + name + '"][value="' + value + '"]').checked = true;
        });
    });


});
document.addEventListener('DOMContentLoaded', function () {
    // Функция для скрытия уведомления
    function hideNotification() {
        var notificationContainer = document.querySelector('.notification-container');
        if (notificationContainer) {
            notificationContainer.classList.remove('show-notification');
        }
    }

    var notificationContainer = document.querySelector('.notification-container');
    if (notificationContainer) {
        setTimeout(hideNotification, 1500);
    }
});
