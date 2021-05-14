<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:50:34
 */

declare(strict_types = 1);
namespace dicr\p1sms\entity;

use dicr\json\EntityValidator;
use dicr\p1sms\Entity;
use dicr\p1sms\P1Sms;

/**
 * Схема каскадных сообщений.
 */
class SchemeDetail extends Entity
{
    /**
     * @var string Статус, при котором должно отправиться каскадное сообщение
     * STATUS_* (delivered, not_delivered, error, read)
     */
    public $needStatus;

    /** @var Template Шаблон сообщения */
    public $smstemplate;

    /**
     * @inheritDoc
     */
    public function attributeEntities(): array
    {
        return [
            'smstemplate' => Template::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['needStatus', 'required'],
            ['needStatus', 'in', 'range' => P1Sms::STATUS],

            ['smstemplate', 'required'],
            ['smstemplate', EntityValidator::class]
        ];
    }
}
