<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.11.20 22:16:18
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use dicr\json\EntityValidator;
use dicr\p1sms\P1SmsRequest;
use yii\base\Exception;

use function gettype;
use function is_array;
use function preg_replace;

/**
 * P1Sms
 */
class SmsRequest extends P1SmsRequest
{
    /** @var string дешевый канал с негарантированной медленной доставкой для массовых рассылок */
    public const CHANNEL_DIGIT = 'digit';

    /** @var string стандартный канал */
    public const CHANNEL_CHAR = 'char';

    /** @var string Viber */
    public const CHANNEL_VIBER = 'viber';

    /** @var string VK */
    public const CHANNEL_VK = 'vk';

    /** @var string Telegram */
    public const CHANNEL_TELEGRAM = 'telegram';

    /** @var string Номер телефона */
    public $phone;

    /** @var string Текст сообщения */
    public $text;

    /** @var ?string Ссылка для подстановки */
    public $link;

    /** @var string Канал сообщений (digit, char, viber, vk, telegram) */
    public $channel = self::CHANNEL_CHAR;

    /** @var string Имя отправителя */
    public $sender;

    /** @var int timestamp Количество секунд Unix timestamp */
    public $plannedAt;

    /** @var ?ViberParameters Параметры сообщений Viber */
    public $viberParameters;

    /** @var ?VkParameters Параметры сообщений ВКонтакте */
    public $vkParameters;

    /**
     * @var ?int[] ID чатов в Telegram.
     * ID чатов в Telegram, куда необходимо послать сообщение у одного номера телефона может быть привязано
     * несколько чатов).
     */
    public $telegramChatIds;

    /** @var ?TgParameters Параметры сообщений Telegram */
    public $tgParameters;

    /** @var ?int ID схемы каскадных смс. ID, заранее созданной схемы каскадных сообщений. */
    public $cascadeSchemeId;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['phone', 'required'],
            ['phone', 'filter', 'filter' => static function ($phone) : string {
                return (string)preg_replace('~[\D]+~u', '', $phone);
            }],
            ['phone', 'string', 'length' => 11],

            ['text', 'trim'],
            ['text', 'default'],

            ['link', 'default'],
            ['link', 'string'],

            ['channel', 'required'],
            ['channel', 'in', 'range' => [
                self::CHANNEL_DIGIT, self::CHANNEL_CHAR, self::CHANNEL_VK, self::CHANNEL_VIBER, self::CHANNEL_TELEGRAM
            ]],

            ['sender', 'default'],
            ['sender', 'required', 'when' => function () : bool {
                return $this->channel === self::CHANNEL_CHAR || $this->channel === self::CHANNEL_VIBER;
            }],

            ['plannedAt', 'default'],
            ['plannedAt', 'integer', 'min' => 1],
            ['plannedAt', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['telegramChatIds', 'default'],
            ['telegramChatIds', function (string $attribute) {
                if (empty($this->{$attribute})) {
                    $this->{$attribute} = null;
                } elseif (! is_array($this->{$attribute})) {
                    $this->addError($attribute, 'Некорректный тип: ' . gettype($this->{$attribute}));
                }
            }],

            ['cascadeSchemeId', 'default'],
            ['cascadeSchemeId', 'integer', 'min' => 1],
            ['cascadeSchemeId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['viberParameters', EntityValidator::class, 'class' => ViberParameters::class],
            ['viberParameters', 'required', 'when' => function () : bool {
                return $this->channel === self::CHANNEL_VIBER;
            }],

            ['vkParameters', EntityValidator::class, 'class' => VkParameters::class],
            ['vkParameters', 'required', 'when' => function () : bool {
                return $this->channel === self::CHANNEL_VK;
            }],

            ['tgParameters', EntityValidator::class, 'class' => TgParameters::class],
            ['tgParameters', 'required', 'when' => function () : bool {
                return $this->channel === self::CHANNEL_TELEGRAM;
            }]
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeEntities() : array
    {
        return [
            'viberParameters' => ViberParameters::class,
            'vkParameters' => VkParameters::class,
            'tgParameters' => TgParameters::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getJson() : array
    {
        return [
            'sms' => [
                parent::getJson()
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function url() : string
    {
        return 'apiSms/create';
    }

    /**
     * Отправить сообщение.
     *
     * @return array результаты отправки сообщения
     * @throws Exception
     */
    public function send() : array
    {
        /** @var array $data */
        $data = parent::send();

        // получаем результат отправки одного сообщения
        $data = array_shift($data);

        if (empty($data) || empty($data['status'])) {
            throw new Exception('Нет результата отправки');
        }

        return $data;
    }
}
