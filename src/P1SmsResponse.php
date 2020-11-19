<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.11.20 21:56:46
 */

declare(strict_types = 1);
namespace dicr\p1sms;

/**
 * Ответ от P1Sms.
 */
class P1SmsResponse extends P1SMSEntity
{
    /** @var string */
    public const STATUS_SUCCESS = 'success';

    /** @var string рассылка создана */
    public const STATUS_CREATED = 'created';

    /** @var string рассылка на модерации */
    public const STATUS_MODERATION = 'moderation';

    /** @var string сообщение отправлено */
    public const STATUS_SENT = 'sent';

    /** @var string ошибка в системе */
    public const STATUS_ERROR = 'error';

    /** @var string сообщение доставлено */
    public const STATUS_DELIVERED = 'delivered';

    /** @var string сообщение не доставлено */
    public const STATUS_NOT_DELIVERED = 'not_delivered';

    /** @var string сообщение прочитано */
    public const STATUS_READ = 'read';

    /** @var string сообщение запланировано */
    public const STATUS_PLANNED = 'planned';

    /** @var string низкий баланс клиента */
    public const STATUS_LOW_BALANCE = 'low_balance';

    /** @var string Ошибка 592 */
    public const STATUS_LOW_PARTNER_BALANCE = 'low_partner_balance';

    /** @var string сообщение отклонено */
    public const STATUS_REJECTED = 'rejected';

    /** @var string */
    public $status;

    /** @var mixed */
    public $data;
}
