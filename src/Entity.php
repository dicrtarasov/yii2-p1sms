<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 02:00:32
 */

declare(strict_types = 1);
namespace dicr\p1sms;

use dicr\json\JsonEntity;

/**
 * Модель данных JSON.
 */
abstract class Entity extends JsonEntity
{
    /**
     * @inheritDoc
     */
    public function attributeFields(): array
    {
        // не переопределяем названия полей
        return [];
    }
}
