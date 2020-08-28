<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 28.08.20 06:46:56
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use dicr\p1sms\JsonEntity;

use function is_array;

/**
 * Параметры Telegram.
 */
class TgParameters extends JsonEntity
{
    /** @var string Username Telegram Бота */
    public $botUsername;

    /** @var array массив параметров сообщений. Подробности - в документации API */
    public $content;

    /**
     * @inheritDoc
     */
    public function rules()
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
