Для корректной работы нужно еще установить пакет https://github.com/arturishe21/mail-templates

В composer.json добавляем в блок require
```json
 "artur/registration": "1.0.*"
```

Выполняем
```json
composer update
```

Добавляем в app.php
```php
  'Vis\Registration\RegistrationServiceProvider',
```

Публикуем js файлы
```json
   php artisan asset:publish artur/registration
```

Публикуем config
```json
   php artisan config:publish artur/registration
```

Публикуем views
```json
   php artisan view:publish artur/registration
```

Вызов  формы авторизации в вьюхе
```php
    @include('registration::authorization_form')
```

Вызов формы напоминание пароля в вьюхе
```php
    @include('registration::forgot_pass_form')
```

Вызов формы регистрация  в вьюхе
```php
    @include('registration::registration_form')
```
