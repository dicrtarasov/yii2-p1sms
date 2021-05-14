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
 * Информация о сообщении.
 */
class Info extends Entity
{
    /** @var int|string */
    public $id;

    /** @var ?string */
    public $errorDescription;

    /** @var float|string */
    public $cost;

    /** @var int timestamp */
    public $createdAt;

    /** @var int timestamp */
    public $updatedAt;

    /** @var ?int */
    public $cascadeSmsId;

    /** @var string (STATUS_*) */
    public $status;
}
