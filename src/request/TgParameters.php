<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.11.20 22:08:42
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use dicr\p1sms\P1SMSEntity;

use function is_array;

/**
 * Параметры Telegram.
 */
class TgParameters extends P1SMSEntity
{
    /** @var string Username Telegram Бота */
    public $botUsername;

    /** @var array массив параметров сообщений. Подробности - в документации API */
    public $content;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['botUsername', 'required'],
            ['botUsername', 'string'],

            ['content', 'required'],
            ['content', function ($attribute) {
                if (empty($this->{$attribute}) || ! is_array($this->{$attribute})) {
                    $this->addError($attribute, 'Необходимо заполнить контент');
                }
            }]
        ];
    }
}
