<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.11.20 22:05:02
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\json\JsonEntity;

/**
 * Модель данных JSON.
 */
abstract class P1SMSEntity extends JsonEntity
{
    /**
     * @inheritDoc
     */
    public function attributeFields() : array
    {
        // не переопределяем названия полей
        return [];
    }
}
