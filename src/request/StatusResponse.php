<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:51:27
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use dicr\p1sms\entity\Status;
use dicr\p1sms\P1SmsResponse;

/**
 * Class StatusResponse
 *
 * @property Status[] $data
 */
class StatusResponse extends P1SmsResponse
{
    /**
     * @inheritDoc
     */
    public function attributeEntities(): array
    {
        return [
            'data' => [Status::class]
        ];
    }
}
