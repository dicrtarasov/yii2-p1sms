<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:48:39
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use dicr\p1sms\P1SmsRequest;

/**
 * Запрос информации о сообщении
 */
class InfoRequest extends P1SmsRequest
{
    /** @var int[] Список ID сообщений */
    public $apiSmsIdList;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['apiSmsIdList', 'required'],
            ['apiSmsIdList', 'each', 'rule' => ['integer', 'min' => 1]],
            ['apiSmsIdList', 'each', 'rule' => ['filter', 'filter' => 'intval']]
        ];
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return 'apiSms/get';
    }

    /**
     * @inheritDoc
     * @return InfoResponse
     */
    protected function createResponse(array $json): InfoResponse
    {
        return new InfoResponse(['json' => $json]);
    }

    /**
     * @inheritDoc
     * @return InfoResponse
     */
    public function send(): InfoResponse
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::send();
    }
}
