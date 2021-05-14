<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 14.05.21 23:59:59
 */

declare(strict_types = 1);
namespace dicr\p1sms;

/**
 * Ответ от P1Sms.
 */
class P1SmsResponse extends Entity
{
    /** @var string */
    public $status;

    /** @var mixed */
    public $data;
}
