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
 * Параметры сообщения Viber.
 */
class Viber extends Entity
{
    /** @var string сообщение без кнопки */
    public const TYPE_TEXT = 'text';

    /** @var string сообщение с кнопкой, открывающей ссылку */
    public const TYPE_LINK = 'link';

    /** @var string сообщение с кнопкой с номером телефона */
    public const TYPE_PHONE = 'phone';

    /** @var string Тип сообщения Viber */
    public $type;

    /** @var ?string Текст кнопки */
    public $btnText;

    /** @var ?string Ссылка кнопки */
    public $btnLink;

    /** @var ?string Номер кнопки */
    public $btnPhone;

    /** @var ?string Хэш картинки. Возвращается в результате запроса на загрузку картинки */
    public $imageHash;

    /** @var ?int Время жизни сообщения, сек (от 60 до 86400) */
    public $smsLifetime;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['type', 'required'],
            ['type', 'in', 'range' => [
                self::TYPE_TEXT, self::TYPE_LINK, self::TYPE_PHONE
            ]],

            ['btnText', 'trim'],
            ['btnText', 'default'],
            ['btnText', 'require',
                'when' => fn(): bool => $this->type === self::TYPE_TEXT || $this->type === self::TYPE_PHONE],

            ['btnLink', 'trim'],
            ['btnLink', 'default'],
            ['btnLink', 'required', 'when' => fn(): bool => $this->type === self::TYPE_LINK],

            ['btnPhone', 'trim'],
            ['btnPhone', 'default'],
            ['btnPhone', 'required', 'when' => fn(): bool => $this->type === self::TYPE_PHONE],
            ['btnPhone', 'string', 'max' => 11],

            ['imageHash', 'trim'],
            ['imageHash', 'default'],

            ['smsLifetime', 'default'],
            ['smsLifetime', 'integer', 'min' => 60, 'max' => 86400],
            ['smsLifetime', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
        ];
    }
}
