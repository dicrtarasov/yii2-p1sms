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
 * Результат отправки сообщения.
 */
class Result extends Entity
{
    /** @var string */
    public $message;

    /** @var int */
    public $id;

    /** @var string */
    public $status;

    /** @var string */
    public $phone;
}
