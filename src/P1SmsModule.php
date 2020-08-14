<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.08.20 10:09:31
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\validate\ValidateException;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\helpers\Json;
use yii\httpclient\Client;

use function array_filter;
use function array_map;
use function gettype;
use function is_callable;
use function is_object;

/**
 * Клиент p1sms.
 *
 * @link https://admin.p1sms.ru/panel/apiinfo
 */
class P1SmsModule extends Module
{
    /** @var string API URL */
    public const URL_API = 'https://admin.p1sms.ru/apiSms';

    /** @var string API URL */
    public $url = self::URL_API;

    /** @var string API Key */
    public $apiKey;

    /** @var array опции http-клиента */
    public $clientConfig = [];

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

    /**
     * Фильтрует параметры.
     *
     * @param array $params
     * @return array
     */
    public static function filterParams(array $params): array
    {
        return array_filter(array_map(static function ($param) {
            return is_object($param) ? $param->params() : $param;
        }, $params), static function ($param) {
            return $param !== null && $param !== '' && $param !== [];
        });
    }

    /** @var Client */
    private $_client;

    /**
     * Клиент HTTP.
     *
     * @return Client
     * @throws InvalidConfigException
     */
    private function client(): Client
    {
        if (! isset($this->_client)) {
            $config = array_merge([
                'class' => Client::class
            ], $this->clientConfig ?: [], [
                'baseUrl' => $this->url
            ]);

            $this->_client = Yii::createObject($config);
        }

        return $this->_client;
    }

    /**
     * Отправляет запрос.
     *
     * @param string $url относительный URL
     * @param array $data данные
     * @return array данные ответа
     * @throws InvalidConfigException
     * @throws \yii\httpclient\Exception
     * @throws Exception
     */
    private function post(string $url, array $data)
    {
        $request = $this->client()->post($url, array_merge($data, [
            'apiKey' => $this->apiKey
        ]), [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);

        $request->format = Client::FORMAT_JSON;

        Yii::debug('Отправка запроса: ' . Json::encode($data), __METHOD__);

        $response = $request->send();
        if (! $response->isOk) {
            throw new Exception('Ошибка HTTP: ' . $response->statusCode);
        }

        $response->format = Client::FORMAT_JSON;
        $data = $response->data;
        if (! isset($data['status'])) {
            throw new Exception('Некорректный ответ: ' . $response->content);
        }

        if ($data['status'] !== 'success') {
            throw new Exception('Ошибка запроса: ' . $response->content);
        }

        Yii::debug('Получен ответ: ' . Json::encode($data), __METHOD__);

        return $data['data'] ?? [];
    }

    /**
     * Отправка SMS.
     *
     * @param P1Sms[] $smss массив отправляемых SMS
     * @return array данные ответа
     * @throws Exception
     * @throws InvalidConfigException
     * @throws ValidateException
     * @throws \yii\httpclient\Exception
     */
    public function sendSms(array $smss): array
    {
        if (empty($smss)) {
            throw new InvalidArgumentException('empty sms');
        }

        return $this->post('create', [
            'sms' => array_map(static function ($sms) {
                if (! $sms instanceof P1Sms) {
                    throw new InvalidArgumentException('sms: ' . gettype($sms));
                }

                return $sms->params();
            }, $smss)
        ]);
    }
}
