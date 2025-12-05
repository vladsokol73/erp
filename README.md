## ERP Main (основное веб‑приложение)

Laravel‑приложение, реализующее основной пользовательский интерфейс и бизнес‑логику ERP‑системы.

### Основные возможности

- **Управление клиентами, сделками, тикетами** и другими сущностями (см. раздел `app/Models` и миграции).
- **Веб‑интерфейс на Inertia + TypeScript/React (TSX)** — фронтенд лежит в `resources/js` и `public/assets`.
- **Интеграции с внешними сервисами** (например, чат‑сервис, URL shortener, AI‑функции с OpenAI).
- **Работа с очередями, уведомлениями и фоновыми задачами.**

### Технологии

- PHP 8.1+, Laravel 11.
- Inertia.js + TypeScript + Tailwind (см. `resources/js`, `tailwind.config.js`, `tsconfig.json`).
- Redis/Postgres/MySQL (в зависимости от `.env`).

### Базовый запуск локально

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate

php artisan serve      # backend
npm run dev            # фронтенд
```

После запуска приложение будет доступно по адресу, указанному в `APP_URL` (обычно `http://localhost:8000`).


