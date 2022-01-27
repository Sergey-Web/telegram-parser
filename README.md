## Разворачивание на боевом сервере

### Необходимые программы
    - PHP: 8.0.3
    - NGINX: 1.19
    - COMPOSER: 2.1.6
    - PostgreSQL: 13.3
    - Laravel

### Проект расположен в директории
    /notify-parser

### Путь к индексному файлу в директории
root /var/www/notify-parser/public;

### Путь к .env файлу
/var/www/notify-parser
- Важно! Файл добавить в **vault**


### База данных
    notify-parser

### Команды для загрузки зависимостей
    - composer install
    - php artisan migrate
    - php artisan db:seed


## Поднять докер окружение

### Команды
- make init - загрузка имеджей, сборка контейнеров, поднятия контейнеров 
- make up - запуск контейнеров
- make down - остановка контейнеров

**Важно!**
Описание и настройка парсера описаны в директории /notify-parse/README.md 