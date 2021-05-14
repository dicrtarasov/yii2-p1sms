<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 02:00:11
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\p1sms\request\CreateRequest;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

use function is_callable;

use const CURLOPT_ENCODING;

/**
 * Клиент p1sms.
 *
 * @property-read Client $httpClient
 * @link https://admin.p1sms.ru/panel/apiinfo
 */
class P1Sms extends Module
{
    /** @var string API URL */
    public const URL_API = 'https://admin.p1sms.ru';

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

    /** @var string[] */
    public const CHANNEL = [
        self::CHANNEL_DIGIT, self::CHANNEL_CHAR, self::CHANNEL_VIBER, self::CHANNEL_VK, self::CHANNEL_TELEGRAM
    ];

    /** @var string */
    public const STATUS_SUCCESS = 'success';

    /** @var string рассылка создана */
    public const STATUS_CREATED = 'created';

    /** @var string рассылка на модерации */
    public const STATUS_MODERATION = 'moderation';

    /** @var string сообщение отправлено */
    public const STATUS_SENT = 'sent';

    /** @var string ошибка в системе */
    public const STATUS_ERROR = 'error';

    /** @var string сообщение доставлено */
    public const STATUS_DELIVERED = 'delivered';

    /** @var string сообщение не доставлено */
    public const STATUS_NOT_DELIVERED = 'not_delivered';

    /** @var string сообщение прочитано */
    public const STATUS_READ = 'read';

    /** @var string сообщение запланировано */
    public const STATUS_PLANNED = 'planned';

    /** @var string низкий баланс клиента */
    public const STATUS_LOW_BALANCE = 'low_balance';

    /** @var string Ошибка 592 */
    public const STATUS_LOW_PARTNER_BALANCE = 'low_partner_balance';

    /** @var string сообщение отклонено */
    public const STATUS_REJECTED = 'rejected';

    /** @var string[] */
    public const STATUS = [
        self::STATUS_SUCCESS, self::STATUS_CREATED, self::STATUS_MODERATION, self::STATUS_SENT,
        self::STATUS_ERROR, self::STATUS_DELIVERED, self::STATUS_NOT_DELIVERED,
        self::STATUS_READ, self::STATUS_PLANNED, self::STATUS_LOW_BALANCE, self::STATUS_LOW_PARTNER_BALANCE,
        self::STATUS_REJECTED
    ];

    /** @var string API URL */
    public $url = self::URL_API;

    /** @var string API Key */
    public $apiKey;

    /** @var ?callable */
    public $callback;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if (empty($this->url)) {
            throw new InvalidConfigException('url');
        }

        if (empty($this->apiKey)) {
            throw new InvalidConfigException('apiKey');
        }

        if (! empty($this->callback) && ! is_callable($this->callback)) {
            throw new InvalidConfigException('callback');
        }

        $this->controllerNamespace = __NAMESPACE__;
    }

    /** @var Client */
    private $_httpClient;

    /**
     * Клиент HTTP.
     *
     * @return Client
     */
    public function getHttpClient(): Client
    {
        if (! isset($this->_httpClient)) {
            $this->_httpClient = new Client([
                'transport' => CurlTransport::class,
                'baseUrl' => $this->url,
                'requestConfig' => [
                    'format' => Client::FORMAT_JSON,
                    'headers' => [
                        'Accept' => 'application/json'
                    ],
                    'options' => [
                        CURLOPT_ENCODING => ''
                    ]
                ],
                'responseConfig' => [
                    'format' => Client::FORMAT_JSON
                ]
            ]);
        }

        return $this->_httpClient;
    }

    /**
     * Создает запрос.
     *
     * @param array $config
     * @return P1SmsRequest
     * @throws InvalidConfigException
     */
    public function request(array $config): P1SmsRequest
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject($config, [$this]);
    }

    /**
     * Создает СМС.
     *
     * @param array $config
     * @return CreateRequest
     */
    public function createRequest(array $config = []): CreateRequest
    {
        return new CreateRequest($this, $config + ['class' => CreateRequest::class]);
    }
}
