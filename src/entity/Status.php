<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:48:39
 */

declare(strict_types = 1);
namespace dicr\p1sms\entity;

use dicr\p1sms\Entity;

/**
 * Статус сообщения.
 */
class Status extends Entity
{
    /** @var int */
    public $id;

    /** @var string (STATUS_*) */
    public $status;

    /** @var string Y-m-d H:i:s */
    public $receiveDate;

    /**
     * @inheritDoc
     */
    public function attributeFields(): array
    {
        return [
            'id' => 'sms_id',
            'status' => 'sms_status',
            'receiveDate' => 'receive_date'
        ];
    }
}
