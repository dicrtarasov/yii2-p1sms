<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 28.08.20 06:08:02
 */

declare(strict_types = 1);
namespace dicr\p1sms;

/**
 * Модель данных JSON.
 */
abstract class JsonEntity extends \dicr\helper\JsonEntity
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
