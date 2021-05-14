<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 15.05.21 01:32:33
 */

declare(strict_types = 1);
namespace dicr\p1sms\request;

use CURLFile;
use dicr\p1sms\P1SmsRequest;

/**
 * Загрузка изображения для Viber
 */
class LoadImageRequest extends P1SmsRequest
{
    /** @var CURLFile Файл с изображением */
    public $img;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['img', 'required']
        ];
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return 'apiSms/loadImage';
    }
}
