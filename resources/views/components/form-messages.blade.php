@props(['prefix' => ''])

<!-- Блок для отображения ошибок -->
<div id="{{ $prefix }}FormErrors" class="mb-4 hidden">
    <div class="p-4 text-sm rounded-lg bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400">
        <p class="error-message"></p>
    </div>
</div>

<!-- Блок для отображения предупреждений -->
<div id="{{ $prefix }}FormWarnings" class="mb-4 hidden">
    <div class="p-4 text-sm rounded-lg bg-yellow-50 dark:bg-yellow-500/10 text-yellow-600 dark:text-yellow-400">
        <p class="warning-message"></p>
    </div>
</div>

<!-- Блок для отображения успешных сообщений -->
<div id="{{ $prefix }}FormSuccess" class="mb-4 hidden">
    <div class="p-4 text-sm rounded-lg bg-green-50 dark:bg-green-500/10 text-green-500 dark:text-green-400">
        <p class="success-message"></p>
    </div>
</div>

<script>
    window.showFormMessage = function(type, message, prefix = '') {
        const elements = {
            error: document.getElementById(prefix + 'FormErrors'),
            warning: document.getElementById(prefix + 'FormWarnings'),
            success: document.getElementById(prefix + 'FormSuccess')
        };

        // Скрываем все сообщения
        Object.values(elements).forEach(el => {
            if (el) el.classList.add('hidden');
        });

        // Показываем нужное сообщение
        if (elements[type]) {
            elements[type].querySelector('p').textContent = message;
            elements[type].classList.remove('hidden');

            // Автоматически скрываем успешные сообщения через 3 секунды
            if (type === 'success') {
                setTimeout(() => {
                    elements[type].classList.add('hidden');
                }, 3000);
            }
        }
    };
</script>
