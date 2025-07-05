# Yii1 + Vue.js To-Do

Небольшое тестовое приложение на **Yii 1.1** (backend, API) и **Vue.js 2** (frontend).

## Как запустить (пошагово)

0. Что нужно заранее:
   * **PHP >= 7.4** (включая `pdo_sqlite` – обычно он уже встроен).
   * **Composer** (менеджер пакетов PHP) – <https://getcomposer.org/download>.

1. Скачайте проект (или клонируйте git-репозиторий):
   ```bash
   git clone https://github.com/username/yii1-vue-todo.git 
   cd yii1-vue-todo
   ```

2. Установите зависимости (это скачает сам фреймворк Yii 1.1):
   ```bash
   composer install --no-interaction
   ```

3. Запустите встроенный веб-сервер PHP (он работает на любом ПК, ничего настраивать не надо):
   ```bash
   php -S localhost:8000 router.php
   ```
   *В Windows просто скопируйте команду в **PowerShell** или **CMD**; в macOS/Linux – в терминал.*

4. Откройте в браузере адрес **http://localhost:8000/**. Должна появиться страница со списком задач и формой добавления.

👉  При первом обращении сервер сам создаст файл базы данных `protected/data/todo.db` (SQLite) и таблицу `task` – ничего руками делать не нужно.

Если видите белую страницу или ошибку 500 – проверьте, что PHP расширение *pdo_sqlite* включено (`php -m | findstr sqlite`).

## API

| Метод | URL         | Параметры | Ответ |
|-------|-------------|-----------|-------|
| GET   | /api/list   | –         | JSON-массив задач |
| POST  | /api/create | title     | JSON-объект добавленной задачи |

Task fields: `id`, `title`, `is_done` (0/1). 