## Posiflora Growth MVP — Telegram интеграция

Проект состоит из:
- **backend**: Symfony 8 + API Platform (PHP 8.4, MySQL)
- **frontend**: React + Vite (отдельный SPA, билд кладётся в `backend/templates/base.html.twig`)
- **инфраструктура**: Docker (PHP-FPM, Caddy, MySQL) + SQL-дамп с тестовыми данными.

### Требования

- **Docker** и **Docker Compose** (v2+)
- Порт **80** и **443** свободны на хосте
- Порт **3306** (MySQL) свободен, если нужен доступ к базе с хоста
- Для локальной разработки фронтенда: **Node.js 18+** и **Yarn 1.x**

---

## Запуск через Docker (основной способ)

Все сервисы (PHP, веб‑сервер, БД) поднимаются одной командой из корня проекта.

### 1. Клонировать репозиторий

```bash
git clone <repo-url> posiflora
cd posiflora
```
Собрать фронтенд (не успел сделать поймал в последний момент): 
```bash
  cd frontend
  yarn install
  yarn build
```

### 2. Запустить docker‑сервисы

В корне проекта (`/home/alk/Work/jobs/posiflora`):

```bash
docker compose up -d --build
```

Что происходит:
- **php**: собирается из `backend/Dockerfile`, монтируется код `./backend` в `/var/www/symfony`
- **webserver**: Caddy из `caddy/Dockerfile`, отдаёт `backend/public`
- **database**: MySQL 8.0 с инициализацией из `docker/mysql/init.sql` (создание схемы и тестовые данные)

Переменные окружения (опционально через `.env` или экспорт в оболочке):
- **APP_ENV** (по умолчанию `dev`)
- **APP_SECRET** (по умолчанию `change_this_secret`)
- **DATABASE_URL** (по умолчанию `mysql://symfony:password@database:3306/telegram_connector`)
- **DB_ROOT_PASSWORD, DB_NAME, DB_USER, DB_PASSWORD** — настройки MySQL (см. `docker-compose.yml`)

### 3. Инициализация backend (если нужно)

В контейнере `php` уже выполняется `composer install` и `cache:clear` при сборке образа.  
Если вы обновили зависимости или код:

```bash
docker compose exec php composer install
docker compose exec php php bin/console cache:clear
```

### 4. Доступ к приложению

- Основной веб‑интерфейс (Caddy → Symfony):  
  `http://localhost`

- API Platform (если настроен по умолчанию):  
  `http://localhost/api`

### 5. Доступ к базе данных

Параметры MySQL по умолчанию (см. `docker-compose.yml` и `docker/mysql/init.sql`):

- **host**: `127.0.0.1`
- **port**: `3306`
- **database**: `telegram_connector`
- **user**: `symfony`
- **password**: `password`

Можно подключаться любым клиентом (DBeaver, DataGrip, `mysql` и т.п.).

### 6. Остановка и удаление контейнеров

Остановить (оставляя данные в volume):

```bash
docker compose down
```

Остановить и удалить тома (с потерей данных БД):

```bash
docker compose down -v
```

---

## Локальная разработка frontend

Frontend — это отдельный Vite‑проект в каталоге `frontend`.

### 1. Установка зависимостей

```bash
cd frontend
yarn install
```

### 2. Запуск dev‑сервера

```bash
yarn dev
```

По умолчанию Vite поднимется на `http://localhost:5173` (порт может отличаться, смотрите вывод команды).

### 3. Production‑сборка frontend и интеграция с Symfony

Сборка:

```bash
cd frontend
yarn build
```

Скрипт `postbuild` автоматически скопирует сгенерированный `index.html` в:

- `backend/templates/base.html.twig`

После этого Symfony будет использовать свежий билд SPA в качестве основного шаблона.






