<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:50:34
 */

declare(strict_types = 1);
namespace dicr\p1sms\entity;

use dicr\p1sms\Entity;

/**
 * Параметры Telegram.
 */
class Tg extends Entity
{
    /** @var string Username Telegram Бота */
    public $botUsername;

    /** @var ?array массив параметров сообщений. Подробности - в документации API */
    public $content;

    /** @var ?bool Необходимо ли посылать кнопку 'прочтено' с сообщением (По умолчанию посылается) */
    public $readButton;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['botUsername', 'required'],
            ['botUsername', 'string'],

            ['content', 'default'],

            ['readButton', 'default'],
            ['readButton', 'boolean'],
            ['readButton', 'filter', 'filter' => 'boolval', 'skipOnEmpty' => true]
        ];
    }
}
