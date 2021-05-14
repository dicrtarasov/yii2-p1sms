<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 00:59:16
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use dicr\json\EntityValidator;
use dicr\p1sms\entity\Message;
use dicr\p1sms\P1SmsRequest;
use yii\base\Exception;

/**
 * P1Sms
 */
class CreateRequest extends P1SmsRequest
{
    /** @var Message[] */
    public $sms;

    /**
     * @inheritDoc
     */
    public function attributeEntities(): array
    {
        return [
            'sms' => [Message::class],
        ];
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['sms', 'required'],
            ['sms', EntityValidator::class]
        ];
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return 'apiSms/create';
    }

    /**
     * @inheritDoc
     * @return CreateResponse
     */
    protected function createResponse(array $json): CreateResponse
    {
        return new CreateResponse(['json' => $json]);
    }

    /**
     * Отправить сообщение.
     *
     * @return CreateResponse результаты отправки сообщения
     * @throws Exception
     */
    public function send(): CreateResponse
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::send();
    }
}
