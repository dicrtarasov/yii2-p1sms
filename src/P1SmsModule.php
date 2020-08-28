<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 28.08.20 06:28:48
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\p1sms\request\SmsRequest;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\httpclient\Client;

use function is_callable;

/**
 * Клиент p1sms.
 *
 * @property-read Client $httpClient
 *
 * @link https://admin.p1sms.ru/panel/apiinfo
 */
class P1SmsModule extends Module
{
    /** @var string API URL */
    public const URL_API = 'https://admin.p1sms.ru';

    /** @var string API URL */
    public $url = self::URL_API;

    /** @var string API Key */
    public $apiKey;

    /** @var array опции http-клиента */
    public $httpClientConfig = [];

    /** @var array конфиг СМС */
    public $smsRequestConfig = [];

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init()
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
     * @throws InvalidConfigException
     */
    public function getHttpClient(): Client
    {
        if (! isset($this->_httpClient)) {
            $config = array_merge([
                'class' => Client::class
            ], $this->httpClientConfig ?: [], [
                'baseUrl' => $this->url
            ]);

            $this->_httpClient = Yii::createObject($config);
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
    public function createRequest(array $config): P1SmsRequest
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject($config, [$this]);
    }

    /**
     * Создает СМС.
     *
     * @param array $config
     * @return SmsRequest
     * @throws InvalidConfigException
     */
    public function createSmsRequest(array $config = []): SmsRequest
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createRequest(array_merge([
            'class' => SmsRequest::class
        ], $this->smsRequestConfig ?: [], $config));
    }
}
