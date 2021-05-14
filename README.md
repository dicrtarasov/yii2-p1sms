# P1SMS клиент для Yii2

API: https://admin.p1sms.ru/panel/apiinfo (реализованы не все запросы)

## Конфигурация

```php
$config = [
    'modules' => [
        'p1sms' => [
            'class' => dicr\p1sms\P1Sms::class,
            'apiKey' => 'XXXXXXXXXXX',
        ]
    ]
];
```

## Использование

```php
use dicr\p1sms\P1Sms;

/** @var P1Sms $module получаем модуль */ 
$module = Yii::$app->getModule('p1sms');

// создание запроса
$req = $module->createRequest([
    'phone' => '+71111111111',
    'text' => 'Проверка',
]);

// отправка СМС
$req->send();
```
