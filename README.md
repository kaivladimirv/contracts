[![code style](https://github.com/kaivladimirv/contracts/actions/workflows/code-style.yml/badge.svg)](https://github.com/kaivladimirv/contracts/actions/workflows/code-style.yml)
[![type coverage](https://shepherd.dev/github/kaivladimirv/contracts/coverage.svg)](https://shepherd.dev/github/kaivladimirv/contracts)
[![psalm level](https://shepherd.dev/github/kaivladimirv/contracts/level.svg)](https://psalm.dev/)
[![tests](https://github.com/kaivladimirv/contracts/actions/workflows/tests.yml/badge.svg)](https://github.com/kaivladimirv/contracts/actions/workflows/tests.yml)
[![sast](https://github.com/kaivladimirv/contracts/actions/workflows/sast.yml/badge.svg)](https://github.com/kaivladimirv/contracts/actions/workflows/sast.yml)
![license](https://img.shields.io/badge/license-MIT-green)
<a href="https://php.net"><img src="https://img.shields.io/badge/php-8.3-%238892BF" alt="PHP Programming Language"></a>

## Сервис для работы с договорами страхования
Сервис позволяет страховым компания
работать с договорами, застрахованными лицами,
с возможностью регистрировать услуги,
которые были оказаны застрахованным лицам,
в медицинских организациях.
***

## Возможности
- Единая база договоров, застрахованных лиц и услуг;
- Определение лимитов на услуги: по сумме или количеству;
- Возможность регистрировать и отменять регистрацию оказанных услуг;
- Исключены случаи превышения лимитов по договорам
  при оказании услуг застрахованным лицам;
- Возможность разрешать, индивидуально для застрахованных лиц, превышать лимит на услуги;
- Получение остатков по застрахованному лицу;
- Быстрое получение списка должников;
- Уведомление застрахованных лиц (по электронной почте или телеграм) об изменении остатков по лимитам;
***

## Требования
* PHP 8.3+
* PostgreSQL
* Redis
* RabbitMQ
* Supervisor
***

## Технологии
* PHP 8.3+
* PostgreSQL
* Redis
* RabbitMQ
* Supervisor
* Custom PHP Framework
    - FrontController
    - Config
    - Console
    - Command
    - QueryBuilder
    - DIContainer (PSR-11)
    - Validator
    - Form
    - ServerRequest/Response (PSR-7)
    - Middleware (PSR-15)
    - Pipeline
    - Router
* EventDispatcher (PSR-14)
* Cache (PSR-6)
* Hydrator
* MadelineProto
* ApiDoc
* DDD
***

## Установка

1. Клонировать репозиторий
   ```
   git clone https://github.com/kaivladimirv/insurance-contracts.git
   ```
2. Перейти в директорию проекта
   ```
   cd insurance-contracts
   ```
3. Запуск проекта  
   При первоначальном запуске проекта выполнить команду
   ```
   make init
   ```
   Данная команда установит зависимости, создаст базу данных и запустит проект.  
   В дальнейшем для запуска проекта достаточно выполнять команду:
   ```
   make up
   ```

   Для остановки проекта нужно выполнить
   ```
   make down
   ```

   Для перезапуска проекта выполнить
   ```
   make restart
   ```
***

## Уведомления
Для работы уведомлений в Telegram, необходимо определить переменные в файле .env:
   ```
   TELEGRAM_APP_API_ID
   TELEGRAM_APP_API_HASH
   TELEGRAM_PHONE_NUMBER
   ```

   Далее нужно авторизоваться в Telegram, для этого выполните команду:
   ```   
   make telegram-login
   ```
   Во время выполнения данной команды нужно будет ввести код подтверждения.
   Код подтверждения будет отправлен в Telegram на номер телефона указанный в TELEGRAM_PHONE_NUMBER.

   Для сброса текущего состояния авторизации (закрытия сессии) нужно выполнить команду:
   ```   
   make telegram-logout
   ```
***

## Дополнительные команды для управления сервисом
- Для очистки кэша использовать команду:
   ```   
   make clear-cache
   ```
***

## Документация
Вся документация по API будет доступна после запуска проекта по адресу [http://localhost/](http://localhost/).
***

## Лицензия
Проект Contracts лицензирован для использования в соответствии с лицензией MIT (MIT).
Дополнительную информацию см. в разделе [LICENSE](/LICENSE).
