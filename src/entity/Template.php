<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 02:01:16
 */

declare(strict_types = 1);
namespace dicr\p1sms\entity;

use dicr\json\EntityValidator;
use dicr\p1sms\Entity;
use dicr\p1sms\P1Sms;

/**
 * Шаблон сообщения.
 */
class Template extends Entity
{
    /** @var string (CHANNEL_*) Канал сообщений (digit, char, viber, vk, telegram */
    public $channel;

    /** @var ?string Имя отправителя (обязательно если канал viber, char) */
    public $sender;

    /** @var string[] тексты сообщений */
    public $texts;

    /** @var ?int Задержка отправки сообщения (в минутах) */
    public $minutesDelay;

    /**
     * @var ?int Время, от которого можно отправлять сообщения.
     * Количество минут с начала дня по UTC, например, 10:00 по МСК = 420 минут с начала дня по UTC
     */
    public $sendStartTime;

    /**
     * @var ?int Время, до которого можно отправлять сообщения.
     * Количество минут с начала дня по UTC, например, 10:00 по МСК = 420 минут с начала дня по UTC
     */
    public $sendEndTime;

    /** @var ?Viber */
    public $viberParameters;

    /** @var ?Vk */
    public $vkParameters;

    /** @var ?Tg */
    public $tgParameters;

    /**
     * @inheritDoc
     */
    public function attributeEntities(): array
    {
        return [
            'viberParameters' => Viber::class,
            'vkParameters' => Vk::class,
            'tgParameters' => Tg::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['channel', 'required'],
            ['channel', 'in', 'range' => P1Sms::CHANNEL],

            ['sender', 'trim'],
            ['sender', 'default'],
            ['sender', 'required', 'when' => fn(): bool => $this->channel === P1Sms::CHANNEL_VIBER ||
                $this->channel === P1Sms::CHANNEL_CHAR],

            ['texts', 'required'],
            ['texts', 'each', 'rule' => ['trim']],
            ['texts', 'each', 'rule' => ['required']],

            [['minutesDelay', 'sendStartTime', 'sendEndTime'], 'default'],
            [['minutesDelay', 'sendStartTime', 'sendEndTime'], 'integer', 'min' => 0],
            [['minutesDelay', 'sendStartTime', 'sendEndTime'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['viberParameters', 'vkParameters', 'tgParameters'], 'default'],
            [['viberParameters', 'vkParameters', 'tgParameters'], EntityValidator::class],
            ['viberParameters', 'required', 'when' => fn(): bool => $this->channel === P1Sms::CHANNEL_VIBER],
            ['vkParameters', 'required', 'when' => fn(): bool => $this->channel === P1Sms::CHANNEL_VK],
            ['tgParameters', 'required', 'when' => fn(): bool => $this->channel === P1Sms::CHANNEL_TELEGRAM]
        ];
    }
}
