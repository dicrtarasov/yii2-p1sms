<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.11.20 22:21:09
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\validate\ValidateException;
use Yii;
use yii\base\Exception;
use yii\httpclient\Client;

use function array_merge;

/**
 * Запрос P1Sms
 */
abstract class P1SmsRequest extends P1SMSEntity
{
    /** @var P1SmsModule */
    protected $_module;

    /**
     * P1SmsRequest constructor.
     *
     * @param P1SmsModule $module
     * @param array $config
     */
    public function __construct(P1SmsModule $module, $config = [])
    {
        $this->_module = $module;

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
        return 'post';
    }

    /**
     * Отправить сообщение.
     *
     * @return array результаты отправки (переопределяется в наследнике)
     * @throws Exception
     */
    public function send() : array
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        $request = $this->_module->httpClient->createRequest()
            ->setMethod($this->method())
            ->setUrl($this->url())
            ->setData(array_merge(['apiKey' => $this->_module->apiKey] + $this->json))
            ->setFormat(Client::FORMAT_JSON)
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]);

        Yii::debug('Отправка запроса: ' . $request->toString(), __METHOD__);

        $response = $request->send();
        if (! $response->isOk) {
            throw new Exception('Ошибка HTTP: ' . $response->statusCode);
        }

        Yii::debug('Получен ответ: ' . $response->content, __METHOD__);

        $response->format = Client::FORMAT_JSON;
        $p1response = new P1SmsResponse();
        $p1response->setJson($response->data);

        if ($p1response->status !== P1SmsResponse::STATUS_SUCCESS) {
            throw new Exception('Ошибка запроса: ' . $response->content);
        }

        return $p1response->data;
    }
}
