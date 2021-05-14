<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:55:58
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use dicr\p1sms\P1SmsRequest;

/**
 * Запрос статуса сообщения
 */
class StatusRequest extends P1SmsRequest
{
    /** @var int[] Список ID сообщений */
    public $smsId;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['smsId', 'required'],
            ['smsId', 'each', 'rule' => ['integer', 'min' => 1]],
            ['smsId', 'each', 'rule' => ['filter', 'filter' => '\intval']]
        ];
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return 'apiSms/getSmsStatus';
    }

    /**
     * {@inheritDoc}
     * @return StatusResponse
     */
    protected function createResponse(array $json): StatusResponse
    {
        return new StatusResponse(['json' => $json]);
    }

    /**
     * {@inheritDoc}
     * @return StatusResponse
     */
    public function send(): StatusResponse
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::send();
    }
}
