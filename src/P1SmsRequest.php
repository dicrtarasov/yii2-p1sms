<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:28:31
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\helper\Log;
use dicr\validate\ValidateException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\httpclient\Request;

/**
 * Запрос P1Sms
 */
abstract class P1SmsRequest extends Entity
{
    /** @var P1Sms */
    protected $module;

    /**
     * P1SmsRequest constructor.
     *
     * @param P1Sms $module
     * @param array $config
     */
    public function __construct(P1Sms $module, array $config = [])
    {
        $this->module = $module;

        parent::__construct($config);
    }

    /**
     * Url функции API.
     *
     * @return string
     */
    abstract public function url(): string;

    /**
     * HTTP-метод.
     *
     * @return string
     * @noinspection PhpMethodMayBeStaticInspection
     */
    public function method(): string
    {
        return 'POST';
    }

    /**
     * Создает ответ.
     *
     * @param array $json
     * @return P1SmsResponse
     */
    protected function createResponse(array $json): P1SmsResponse
    {
        return new P1SmsResponse(['json' => $json]);
    }

    /**
     * HTTP-запрос.
     *
     * @return Request
     * @throws InvalidConfigException
     */
    protected function httpRequest(): Request
    {
        return $this->module->httpClient->createRequest()
            ->setMethod($this->method())
            ->setUrl($this->url())
            ->setData($this->json);
    }

    /**
     * Отправить сообщение.
     *
     * @return P1SmsResponse результаты отправки (переопределяется в наследнике)
     * @throws Exception
     */
    public function send(): P1SmsResponse
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        // запрос
        $req = $this->httpRequest();

        // добавляем apiKey
        $data = $req->data;
        $data['apiKey'] = $this->module->apiKey;
        $req->data = $data;

        Log::debug('Запрос: ' . $req->toString());
        $res = $req->send();
        Log::debug('Ответ: ' . $res->toString());

        if (! $res->isOk) {
            throw new Exception('Ошибка HTTP: ' . $res->statusCode);
        }

        $response = $this->createResponse($res->data);
        if ($response->status !== P1Sms::STATUS_SUCCESS) {
            throw new Exception('Ошибка запроса: ' . $res->content);
        }

        return $response;
    }
}
