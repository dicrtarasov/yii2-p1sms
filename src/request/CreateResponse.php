<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:48:39
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use dicr\p1sms\entity\Result;
use dicr\p1sms\P1SmsResponse;

use function array_merge;

/**
 * Class CreateResponse
 *
 * @property Result[] $data
 */
class CreateResponse extends P1SmsResponse
{
    /**
     * @inheritDoc
     */
    public function attributeEntities(): array
    {
        return array_merge(parent::attributeEntities(), [
            'data' => [Result::class]
        ]);
    }
}
