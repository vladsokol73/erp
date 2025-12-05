function validatePasswords(userId) {
    const password = document.getElementById(`password_${userId}`).value;
    const confirmPassword = document.getElementById(`confirm_password_${userId}`).value;
    const errorElement = document.getElementById(`error_${userId}`);
    const formElement = document.getElementById(`form_edit_${userId}`);

    if (password !== confirmPassword) {
        errorElement.style.display = 'block';

        // Отменяем отправку формы
        formElement.addEventListener('submit', preventFormSubmission);
    } else {
        errorElement.style.display = 'none';

        // Убираем блокировку отправки формы, если пароли совпадают
        formElement.removeEventListener('submit', preventFormSubmission);
    }
}

function errorElement(formElement, error) {
    error.style.display = 'block';
    // Отменяем отправку формы
    formElement.addEventListener('submit', preventFormSubmission);
}

function validatePassword() {
    const password = document.getElementById(`password`).value;
    const confirmPassword = document.getElementById(`confirm_password`).value;
    const errorPass = document.getElementById(`error-pass`);
    const formElement = document.getElementById(`form_create`);

    if (password !== confirmPassword) {
        errorPass.innerHTML = "Incorrect password repeat";
        errorElement(formElement, errorPass);
    } else {
        errorPass.style.display = 'none';
        formElement.removeEventListener('submit', preventFormSubmission);
    }
}

// Функция для предотвращения отправки формы
function preventFormSubmission(event) {
    event.preventDefault();
    console.log('Form submission cancelled due to password mismatch.');
}

// Обработка событий submit для всех форм
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(event) {
        const userId = form.getAttribute('data-user-id');  // Получаем ID пользователя
        validatePasswords(userId);
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // Получаем элемент с id "projectsCreate"
    const projectsCreate = document.getElementById("projectsCreate");

    // Функция для проверки условий и изменения видимости "projectsCreate"
    function checkProjects() {
        const checkboxes = document.querySelectorAll("input[type='checkbox'][name^='permission_']");
        let shouldShow = false;
        let shouldShowOperator = false;
        let shouldShowClient = false;
        const selectChannel = document.getElementById("operator");
        const selectClient = document.getElementById("client");

        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                const guardName = checkbox.getAttribute("data-guard-name");

                // Проверяем условие
                if (guardName === "operators" || guardName === "clients") {
                    shouldShow = true;
                }

                if (guardName === "operators") {
                    shouldShowOperator = true;
                }

                if (guardName === "clients") {
                    shouldShowClient = true;
                }
            }
        });

        // Добавляем или убираем класс "invisible"
        if (shouldShow) {
            projectsCreate.classList.remove("invisible");
        } else {
            projectsCreate.classList.add("invisible");
        }

        if (shouldShowOperator) {
            selectChannel.style.display = 'block';
        } else {
            selectChannel.style.display = 'none';
        }

        if (shouldShowClient) {
            selectClient.style.display = 'block';
        } else {
            selectClient.style.display = 'none';
        }

        if (shouldShowClient && shouldShowOperator) {
            selectChannel.classList.add('ml-5');
        } else {
            selectChannel.classList.remove('ml-5');
        }
    }

    // Запускаем проверку при загрузке страницы
    if (projectsCreate) {
        checkProjects();
    }

    // Запускаем проверку при изменении состояния любого чекбокса
    document.querySelectorAll("input[type='checkbox'][name^='permission_']").forEach((checkbox) => {
        checkbox.addEventListener("change", checkProjects);
    });
});

