<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.08.20 09:46:14
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\validate\ValidateException;
use yii\base\Model;

use function gettype;
use function is_a;
use function is_array;

/**
 * SMS
 */
class SMS extends Model
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
    public $channel;

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
    public function rules()
    {
        return [
            ['phone', 'required'],
            ['phone', 'string', 'max' => 11],

            ['text', 'trim'],
            ['text', 'default'],

            ['link', 'default'],
            ['link', 'string'],

            ['channel', 'required'],
            ['channel', 'in', 'range' => [
                self::CHANNEL_DIGIT, self::CHANNEL_CHAR, self::CHANNEL_VK, self::CHANNEL_VIBER, self::CHANNEL_TELEGRAM
            ]],

            ['sender', 'default'],
            ['sender', 'required', 'when' => function () {
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

            [['viberParameters', 'vkParameters', 'tgParameters'], 'validateParams']
        ];
    }

    /**
     * Проверка параметров канала.
     *
     * @param string $attribute
     */
    public function validateParams(string $attribute): void
    {
        static $channels = [
            'viberParameters' => self::CHANNEL_VIBER,
            'vkParameters' => self::CHANNEL_VK,
            'tgParameters' => self::CHANNEL_TELEGRAM
        ];

        static $classes = [
            'viberParameters' => ViberParameters::class,
            'vkParameters' => VkParameters::class,
            'tgParameters' => TgParameters::class
        ];

        if ($this->channel !== ($channels[$attribute] ?? '')) {
            $this->{$attribute} = null;
        } elseif (empty($this->{$attribute})) {
            $this->addError($attribute, 'Требуются параметры');
        } else {
            $class = $classes[$attribute];

            if (is_array($this->{$attribute})) {
                $this->{$attribute} = new $class($this->{$attribute});
            }

            if (! is_a($this->{$attribute}, $class, true)) {
                $this->addError(
                    $attribute, 'Некорректный тип: ' . gettype($this->{$attribute})
                );
            } elseif ($this->{$attribute}->validate()) {
                $this->{$attribute} = $this->{$attribute}->params();
            } else {
                $this->addError(
                    $attribute, (new ValidateException($this->{$attribute}))->getMessage()
                );
            }
        }
    }

    /**
     * Параметры JSON.
     *
     * @return array
     * @throws ValidateException
     */
    public function params(): array
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        return P1SmsModule::filterParams($this->attributes);
    }
}
