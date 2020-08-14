<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.08.20 09:09:01
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\validate\ValidateException;
use yii\base\Model;

use function is_array;

/**
 * Параметры Telegram.
 */
class TgParameters extends Model
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

    /**
     * Параметры JSON.
     *
     * @throws ValidateException
     */
    public function params(): array
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        return P1SmsModule::filterParams($this->attributes);
    }
}
