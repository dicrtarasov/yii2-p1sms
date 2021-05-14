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

use function preg_replace;

/**
 * Сообщение.
 */
class Message extends Entity
{
    /** @var string (11) Номер телефона */
    public $phone;

    /** @var ?string Текст сообщения */
    public $text;

    /** @var ?string Ссылка для подстановки */
    public $link;

    /** @var string Канал сообщений (digit, char, viber, vk, telegram) */
    public $channel = P1Sms::CHANNEL_CHAR;

    /** @var ?string Имя отправителя */
    public $sender;

    /** @var ?int timestamp Количество секунд Unix timestamp */
    public $plannedAt;

    /** @var ?Viber Параметры сообщений Viber */
    public $viberParameters;

    /** @var ?Vk Параметры сообщений ВКонтакте */
    public $vkParameters;

    /**
     * @var ?int[] ID чатов в Telegram.
     * ID чатов в Telegram, куда необходимо послать сообщение у одного номера телефона может быть привязано
     * несколько чатов).
     */
    public $telegramChatIds;

    /** @var ?Tg Параметры сообщений Telegram */
    public $tgParameters;

    /** @var ?int ID схемы каскадных смс. ID, заранее созданной схемы каскадных сообщений. */
    public $cascadeSchemeId;

    /**
     * @inheritDoc
     */
    public function attributeEntities(): array
    {
        return [
            'viberParameters' => Viber::class,
            'vkParameters' => Vk::class,
            'tgParameters' => Tg::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['phone', 'filter', 'filter' => static fn($val): string => preg_replace('~[\D]+~u', '', (string)$val)],
            ['phone', 'required'],
            ['phone', 'string', 'length' => 11],

            ['text', 'trim'],
            ['text', 'default'],

            ['link', 'trim'],
            ['link', 'default'],

            ['channel', 'required'],
            ['channel', 'in', 'range' => P1Sms::CHANNEL],

            ['sender', 'trim'],
            ['sender', 'default'],
            ['sender', 'required', 'when' => fn(): bool => $this->channel === P1Sms::CHANNEL_CHAR ||
                $this->channel === P1Sms::CHANNEL_VIBER],

            ['plannedAt', 'default'],
            ['plannedAt', 'integer', 'min' => 1],
            ['plannedAt', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['telegramChatIds', 'default'],
            ['telegramChatIds', 'each', 'rule' => ['integer']],
            ['telegramChatIds', 'each', 'rule' => ['filter', 'filter' => 'intval']],

            [['viberParameters', 'vkParameters', 'tgParameters'], 'default'],
            [['viberParameters', 'vkParameters', 'tgParameters'], EntityValidator::class],
            ['viberParameters', 'required', 'when' => fn(): bool => $this->channel === P1Sms::CHANNEL_VIBER],
            ['vkParameters', 'required', 'when' => fn(): bool => $this->channel === P1Sms::CHANNEL_VK],
            ['tgParameters', 'required', 'when' => fn(): bool => $this->channel === P1Sms::CHANNEL_TELEGRAM],

            ['cascadeSchemeId', 'default'],
            ['cascadeSchemeId', 'integer', 'min' => 1],
            ['cascadeSchemeId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true]
        ];
    }
}
