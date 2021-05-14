<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:50:34
 */

declare(strict_types = 1);
namespace dicr\p1sms\entity;

use dicr\p1sms\Entity;

use function is_array;

/**
 * Параметры сообщения VK.
 */
class Vk extends Entity
{
    /**
     * @var int Идентификатор шаблона.
     * Присваивается и возвращается в запросе на добавление шаблона.
     */
    public $templateId;

    /**
     * @var array Значения переменных шаблона.
     * JSON объект, где ключи имена переменных в шаблоне
     */
    public $tmplData;

    /**
     * @var ?int Идентификатор пользователя, которому нужно доставить уведомление.
     * Предварительно передается в клиентскую библиотеку в установленном приложении.
     */
    public $userId;

    /**
     * @var ?string Push token iOS или Android.
     * Получается libverify или средствами клиента с устройства.
     */
    public $pushToken;

    /**
     * @var ?string iOS-поле aps.
     * APS Dictionary содержит ключ, используемый Apple для того, чтобы отправлять уведомления на устройство.
     */
    public $pushAps;

    /**
     * @var ?bool Указывает, шифровать ли сообщение для приложения.
     * Значения 0 или 1. По умолчанию = 0.
     */
    public $pushEncrypt;

    /**
     * @var ?string IP адрес пользователя.
     * Используется для определения подборов кодов (bruteforce), а так же для лимитирования запросов;
     * Если user_ip не будет указан, то соответствующие рейт-лимиты использоваться не будут.
     */
    public $userIp;

    /**
     * @var ?int Время жизни сообщения в секундах (от 60 до 86400 секунд).
     * По умолчанию сообщение живет вечно. Если сообщение не было доставлено за время, оно не будет
     * доставлено и ttl тарифицировано.
     */
    public $ttl;

    /** @var ?int Время создания сообщения в GMT+0 (Гринвич, unix-time) в формате десятичного числа в секундах
     * с 1 января 1970 года. По умолчанию берется время выполнения запроса на отправку. Используется вместе с
     * параметром для вычисления времени ttl жизни сообщения.
     */
    public $issueTime;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['templateId', 'required'],
            ['templateId', 'integer', 'min' => 1],
            ['templateId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['tmplData', 'required'],
            ['tmplData', function (string $attribute) {
                if (! is_array($this->{$attribute}) || empty($this->{$attribute})) {
                    $this->addError($attribute, 'Требуется указать значения переменных шаблона');
                }
            }],

            ['userId', 'default'],
            ['userId', 'integer', 'min' => 1],
            ['userId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['pushToken', 'default'],
            ['pushToken', 'string'],

            ['pushAps', 'default'],
            ['pushAps', 'string'],

            ['pushEncrypt', 'default'],
            ['pushEncrypt', 'boolean'],
            ['pushEncrypt', 'filter', 'filter' => 'boolval', 'skipOnEmpty' => true],

            ['userIp', 'default'],
            ['userIp', 'ip'],

            ['ttl', 'default'],
            ['ttl', 'integer', 'min' => 60, 'max' => 86400],
            ['ttl', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['issueTime', 'default'],
            ['issueTime', 'integer', 'min' => 1],
            ['issueTime', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
        ];
    }
}
