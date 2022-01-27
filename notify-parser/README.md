## Запуск проекта

Тестовые настройки в .env.example

### 1) Запустить команды DB:

- php artisan migrate - создания таблиц
- php artisan db:seed - создания тестовой задачи id=1

### 2) Авторизация в Телеграме:

- _(через браузер)_ http://localhost:8080/api/auth
    - указать свой номер
    - выбрать раздел "User"
    - и еще раз подтвердить

### 3) Для тестирования работы:

- если уже ранее был развернут то нужно откатить все миграции **php artisan migrate:rollback** (если будет ошибка в
  ручном режими удалить все таблици)
- накатить миграции **php artisan migrate**
- накатить сиды **php artisan db:seed**
- принцип парсинга в postgres **LIKE** так же есть и полнотекстовый поиск, но он сейчас под вопросом
- далее нужно связаться со мной чтоб подвязать группы для парсинга и группы для пушей, так же можно корректировать
  запросы поиска
- команда для запуска парсинга **php artisan message:download**
- команда для запуска крона **php artisan schedule:work**

### 4) CRUD:
#### Создание задачи
пример:

POST api/task
```
{
    "name": "test_task_1",
    "search_text": "не работ,не прац,баг,опять не работ,не работ,проблем,ошибк",
    "search_type": "like",
    "publishers": [
        {
            "name": "yarmoshuk_test",
            "type": "telegram"
        }, 
        {
            "name": "habr_com",
            "type": "telegram"
        }
    ],
    "subscribers": [
        {
            "name": "@yarmoshuksupport",
            "sender": {
                "name": "NotifyServiceParserBot",
                "type": "telegram_bot"
            }
        }
    ]
}
```

#### Обновление задачи:

PUT api/task/{taskId}
```
{
    "name": "test_task_1",
    "search_text": "не работ,не прац,баг,опять не работ,не работ,проблем,ошибк",
    "search_type": "like",
    "publishers": [
        {
            "name": "yarmoshuk_test",
            "type": "telegram"
        }, 
        {
            "name": "habr_com",
            "type": "telegram"
        }
    ],
    "subscribers": [
        {
            "name": "@yarmoshuksupport",
            "sender": {
                "name": "NotifyServiceParserBot",
                "type": "telegram_bot"
            }
        }
    ]
}
```

#### Удаление задачи:

DELETE api/task/{taskId}

#### Вывод задачи:

GET api/task/{taskId}

