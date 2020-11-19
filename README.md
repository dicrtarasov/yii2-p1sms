# P1SMS клиент для Yii2

API: https://admin.p1sms.ru/panel/apiinfo

## Конфигурация

```php
'modules' => [
    'p1sms' => [
        'class' => dicr\p1sms\P1SMSModule::class,
        'apiKey' => 'XXXXXXXXXXX',
    ]
];
```

## Использование

```php
use dicr\p1sms\P1SMSModule;

/** @var P1SMSModule $module получаем модуль */ 
$module = Yii::$app->getModule('p1sms');

// создание запроса
$req = $module->smsRequest([
    'phone' => '+71111111111',
    'text' => 'Проверка',
]);

// отправка СМС
$req->send();
```
