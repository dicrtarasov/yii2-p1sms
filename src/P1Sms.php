<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.08.20 11:08:08
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\validate\ValidateException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Model;

use function gettype;
use function in_array;
use function is_a;
use function is_array;
use function is_object;

/**
 * P1Sms
 */
class P1Sms extends Model
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

    /** @var P1SmsModule */
    private $_module;

    /**
     * P1Sms constructor.
     *
     * @param P1SmsModule $module
     * @param array $config
     */
    public function __construct(P1SmsModule $module, $config = [])
    {
        parent::__construct($config);

        $this->_module = $module;
    }

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
     * @param string $attr
     */
    public function validateParams(string $attr): void
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

        $params = $this->{$attr};

        if ($this->channel !== ($channels[$attr] ?? '')) {
            $params = null;
        } elseif (empty($params)) {
            $this->addError($attr, 'Требуются параметры');
        } else {
            $class = $classes[$attr];

            if (is_array($params)) {
                $params = new $class($params);
            }

            if (! is_object($params) || ! is_a($params, $class, true)) {
                $this->addError(
                    $attr, 'Некорректный тип: ' . gettype($params)
                );
            } elseif ($params->validate()) {
                $params = $params->params();
            } else {
                $this->addError(
                    $attr, (new ValidateException($params))->getMessage()
                );
            }
        }

        $this->{$attr} = $params;
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

    /**
     * Отправить сообщение.
     *
     * @return array результаты отправки
     * @throws Exception
     * @throws ValidateException
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function send(): array
    {
        $ret = $this->_module->post('create', [
            'sms' => [$this->params()]
        ]);

        // получаем результат отправки одного сообщения
        $ret = array_shift($ret);

        if (empty($ret) || empty($ret['status'])) {
            throw new Exception('Нет результата отправки');
        }

        if (in_array($ret['status'], ['error', 'low_balance', 'low_partner_balance', 'rejected'], true)) {
            throw new Exception('Ошибка отправки: ' . ($ret['errorDescription'] ?? $ret['status']));
        }

        return $ret;
    }
}
