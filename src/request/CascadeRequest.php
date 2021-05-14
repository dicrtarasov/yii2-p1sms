<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 00:53:00
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use dicr\json\EntityValidator;
use dicr\p1sms\entity\SchemeDetail;
use dicr\p1sms\P1SmsRequest;

/**
 * Запрос на создание схемы каскадных сообщений
 */
class CascadeRequest extends P1SmsRequest
{
    /** @var ?string Название схемы каскада */
    public $name;

    /** @var ?string Комментарий */
    public $comment;

    /** @var SchemeDetail Схема каскадных сообщений */
    public $schemeDetail;

    /**
     * @inheritDoc
     */
    public function attributeEntities(): array
    {
        return [
            'schemeDetail' => SchemeDetail::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['name', 'trim'],
            ['name', 'default'],

            ['comment', 'trim'],
            ['comment', 'default'],

            ['schemeDetail', 'required'],
            ['schemeDetail', EntityValidator::class]
        ];
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return 'apiSms/create';
    }
}
