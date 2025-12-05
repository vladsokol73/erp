function fetchServerTime() {
    fetch('/time')
        .then(response => response.json())
        .then(data => {
            document.getElementById('serverTime').innerText = data.serverTime;
        })
        .catch(error => console.error('Error fetching server time:', error));
}

// Запрашиваем время с сервера при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    fetchServerTime();
    setInterval(fetchServerTime, 60000); // обновление каждую минуту
});

document.addEventListener('DOMContentLoaded', function() {
    const copyButton = document.getElementById('copyButton');
    const copyMessage = document.getElementById('copyMessage');
    const userId = copyButton.getAttribute('data-user-id'); // Получаем значение user ID из data-атрибута

    copyButton.addEventListener('click', function() {
        // Копирование текста в буфер обмена
        navigator.clipboard.writeText(userId).then(() => {
            // Показать сообщение "Copied"
            copyMessage.classList.remove('hidden');

            // Скрыть сообщение через 3 секунды
            setTimeout(() => {
                copyMessage.classList.add('hidden');
            }, 3000);
        }).catch(err => {
            console.error('Ошибка при копировании текста: ', err);
        });
    });
});

//Function data search tags by value

function multiTagSearcher(event, searcher){
    const text = event.target.value;
    const tagData = 'tag_searcher_' + searcher;
    const dataSelector = '[data-' + tagData + ']';
    let allTags = document.querySelectorAll(dataSelector);
    for (let i in allTags) {
        if (allTags[i].dataset) {
            if (allTags[i].dataset[tagData].toLowerCase().includes(text.toLowerCase())) {
                allTags[i].classList.remove('hidden');
            } else {
                allTags[i].classList.add('hidden');
            }
        }
    }
}

//Initing Tag searcher
(function() {
    const searchInputs = document.querySelectorAll('[data-tag_searcher]');
    searchInputs.forEach((searchInput)=> {
        const searcher = searchInput.dataset.tag_searcher;
        searchInput.addEventListener("input", (event)=> { multiTagSearcher(event, searcher) });
    })
})();

//Init Flatpickr
document.addEventListener('DOMContentLoaded', function () {
    flatpickr('#dateRange', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        allowInput: true,
        onClose: function(selectedDates, dateStr) {
            // Если нужно дополнительное форматирование, используйте этот обработчик
            console.log('Selected date range:', dateStr);
        }
    });
});

// Init Choices for select
const defaultSelect = () => {
    const elements = document.querySelectorAll('.select');
    elements.forEach(select => {
        if (select.choices) {
            select.choices.destroy(); // Уничтожаем старый Choices
        }
        select.choices = new Choices(select, {
            searchPlaceholderValue: 'Search...',
            removeItemButton: true,
            maxItemCount: 6,
            searchFloor: 3,
            shouldSort: false,
        });
    });
};

// Вызываем функцию после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    defaultSelect(); // Инициализация выбора после загрузки DOM
});
