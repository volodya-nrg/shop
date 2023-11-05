# Настройка debug-а и тестов

```
Не забываем включать listenerPHPDebuger (сверху-справа на панели).
Ссылаемся в настройках на контейнер и в нем интерпритатор php, а так же указываем файл phpunit.phar файл.

Для браузера ставим плагин: https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc
Включаем в браузере дебаг.
Заходим в настройкий->php->servers и где находится mapping проекта то на папку volume указываем /var/www.
Так брекпоинты будут видить и внутренние файлы в докер-контейнере.
```