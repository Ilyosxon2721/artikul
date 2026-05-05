# Artikul

**Artikul** — вертикальная биржа задач для индустрии маркетплейсов. Платформа соединяет продавцов на Wildberries, Ozon, Uzum Market, Yandex Market и других маркетплейсах с профильными исполнителями: менеджерами магазинов, дизайнерами карточек, фотографами товарки, специалистами по внутренней рекламе, аналитиками, копирайтерами и бухгалтерами по маркетплейсам.

- Сайт: [artikul.uz](https://artikul.uz)
- Языки: RU · UZ · EN
- Регион: СНГ (Узбекистан, Россия, Казахстан, Беларусь, Кыргызстан, Армения, Грузия)
- ТЗ MVP: [docs/artikul_tz.docx](docs/artikul_tz.docx)
- Промпты по фазам: [docs/artikul_prompts.docx](docs/artikul_prompts.docx)

## Стек

- **PHP 8.3+** · **Laravel 12 (LTS)**
- **Filament 3** — админ-панель `/admin`
- **Livewire 3** + **Alpine.js 3** — реактивный фронт
- **Laravel Reverb** — WebSockets для чата и уведомлений
- **Laravel Sanctum** — API-токены для будущего мобильного приложения
- **Laravel Horizon** — мониторинг очередей Redis
- **Laravel Scout** + **Meilisearch 1.10** — поиск с фасетами и поддержкой кириллицы/латиницы
- **MySQL 8** · **Redis 7**
- **Spatie**: Permission, Translatable, MediaLibrary, ModelStates, Backup
- **Tailwind CSS 4** · **TipTap** (markdown-редактор)

## Требования

- PHP 8.3 + расширения: mbstring, xml, mysql, redis, gd, intl, zip, bcmath, pdo_mysql
- MySQL 8.0
- Redis 7
- Node.js 22 LTS + npm
- Composer 2
- Meilisearch 1.10 (отдельный сервис)

## Локальная установка

```bash
# 1. Склонировать репозиторий
git clone git@github.com:ilyosxon/artikul.git
cd artikul

# 2. Установить зависимости
composer install
npm install

# 3. Подготовить окружение
cp .env.example .env
php artisan key:generate

# 4. Создать БД artikul и прописать креды в .env
mysql -u root -e "CREATE DATABASE artikul CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Запустить миграции и сидеры
php artisan migrate --seed

# 6. Сборка фронта
npm run build  # или npm run dev для разработки

# 7. Запустить сервер
php artisan serve
# или через Laravel Herd: artikul.test
```

### Дополнительно

```bash
# WebSockets (для чата и live-уведомлений)
php artisan reverb:start

# Очереди (через Redis)
php artisan queue:work

# Horizon (мониторинг очередей)
php artisan horizon
```

## Локализация

- Файлы переводов: `lang/{ru,uz,en}/`
- Текущий язык определяется middleware `App\Http\Middleware\SetLocale`
- Порядок резолва: `?lang=` query → session → user.locale → Accept-Language → `config('app.locale')` (ru)

## Структура

```text
app/
├── Domain/{User,Task,Contract,Review,Search}  # доменные модули
├── Enums/                                     # PHP 8 enums
├── Http/{Controllers,Middleware,Requests,Resources}
├── Models/                                    # Eloquent
├── Services/                                  # бизнес-логика
└── Support/                                   # хелперы

database/
├── migrations/                                # 24 таблицы по ТЗ
└── seeders/                                   # справочники + demo

config/                                        # конфиги Laravel и пакетов
routes/{web,api,channels,console}.php
lang/{ru,uz,en}/                               # переводы
docs/                                          # ТЗ + Claude Code промпты
```

## Окружения

| Окружение  | Домен              | Назначение           |
| ---------- | ------------------ | -------------------- |
| Local      | `artikul.test`     | Разработка (Herd)    |
| Staging    | `dev.artikul.uz`   | Тестирование релизов |
| Production | `artikul.uz`       | Продакшен            |

## Разработка

### Качество кода

```bash
vendor/bin/pint           # форматирование
vendor/bin/phpstan analyse # статический анализ (Larastan)
vendor/bin/pest           # тесты
```

## Фазы MVP

1. **Фаза 1 (3–4 недели)** — каркас: регистрация, профили, каталог задач, каталог исполнителей, базовая Filament-админка, лендинг.
2. **Фаза 2 (3–4 недели)** — сделки и коммуникация: отклики, контракты, чат с Reverb, отзывы blind-method, споры, Telegram-бот.
3. **Фаза 3 (2–3 недели)** — качество и рост: верификация, Meilisearch с фасетами, видеозвонки Jitsi, реферальная программа, публичная статистика.

## Документация

- [docs/phase-1-qa-checklist.md](docs/phase-1-qa-checklist.md) — каркас.
- [docs/phase-2-qa-checklist.md](docs/phase-2-qa-checklist.md) — сделки и коммуникация.
- [docs/phase-3-qa-checklist.md](docs/phase-3-qa-checklist.md) — качество и запуск.
- [docs/admin-guide.md](docs/admin-guide.md) — гайд для админов.
- [docs/dispute-resolution-playbook.md](docs/dispute-resolution-playbook.md) — арбитраж.
- [docs/support-faq.md](docs/support-faq.md) — типовые ответы поддержки.
- [docs/launch-materials.md](docs/launch-materials.md) — пресс-релиз и посты.

## Переменные окружения

Полный список ключей в [`.env.example`](.env.example). Минимально для запуска:

| Группа | Ключи | Обязательно |
|---|---|---|
| App | `APP_NAME`, `APP_KEY`, `APP_URL`, `APP_LOCALE=ru`, `APP_FALLBACK_LOCALE=en`, `APP_TIMEZONE=Asia/Tashkent` | да |
| DB | `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE=artikul`, `DB_USERNAME`, `DB_PASSWORD` | да |
| Cache / queue / session | `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`, `REDIS_HOST`, `REDIS_PASSWORD` | для prod |
| Mail | `MAIL_MAILER=mailgun`, `MAILGUN_DOMAIN`, `MAILGUN_SECRET` (или Resend / SES) | да для prod |
| WebSockets | `BROADCAST_CONNECTION=reverb`, `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`, `REVERB_HOST`, `REVERB_PORT` | для чата |
| SMS (Eskiz) | `ESKIZ_EMAIL`, `ESKIZ_PASSWORD`, `ESKIZ_SENDER_ID=4546`, `ESKIZ_BASE_URL=https://notify.eskiz.uz/api` | для регистрации по телефону |
| Telegram | `TELEGRAM_BOT_TOKEN`, `TELEGRAM_BOT_USERNAME=artikul_bot`, `TELEGRAM_JOBS_CHANNEL` (опц.) | для бота |
| Google OAuth | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URL` | для логина через Google |
| S3 / R2 | `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_BUCKET`, `AWS_ENDPOINT`, `AWS_DEFAULT_REGION`, `AWS_USE_PATH_STYLE_ENDPOINT=true` | для prod (вложения, верификация) |
| Search | `SCOUT_DRIVER=meilisearch`, `MEILISEARCH_HOST`, `MEILISEARCH_KEY` | для поиска |
| Analytics | `PLAUSIBLE_DOMAIN`, `PLAUSIBLE_HOST=https://plausible.io` | опц. |
| Errors | `SENTRY_LARAVEL_DSN`, `SENTRY_TRACES_SAMPLE_RATE=0.2` | для prod |

В тестах (`phpunit.xml`) используются: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `SCOUT_DRIVER=null`,
`CACHE_STORE=array`, `SESSION_DRIVER=array`, `QUEUE_CONNECTION=sync` — внешние сервисы не нужны.

## Деплой

```bash
# первый запуск на VPS
php artisan migrate --force
php artisan db:seed --class=MarketplaceSeeder --force
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=SpecializationSeeder --force
php artisan artikul:make-admin you@artikul.uz --super
php artisan storage:link

# обычный релиз
./deploy.sh
```

## Лицензия

Проприетарный софт. © 2026 Sobirov Ilyoskhon. Все права защищены.
