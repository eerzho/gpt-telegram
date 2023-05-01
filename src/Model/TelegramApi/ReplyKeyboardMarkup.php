<?php

namespace App\Model\TelegramApi;

class ReplyKeyboardMarkup extends \TelegramBot\Api\Types\ReplyKeyboardMarkup
{
    protected static $map = [
        'keyboard' => true,
        'one_time_keyboard' => true,
        'resize_keyboard' => true,
        'selective' => true,
        'is_persistent' => true,
    ];

    protected $isPersistent;

    public function __construct($keyboard = [], $oneTimeKeyboard = null, $resizeKeyboard = null, $selective = null, $isPersistent = null)
    {
        $this->isPersistent = $isPersistent;
        parent::__construct($keyboard, $oneTimeKeyboard, $resizeKeyboard, $selective);
    }

    public function getIsPersistent()
    {
        return $this->isPersistent;
    }

    public function setIsPersistent(mixed $isPersistent)
    {
        $this->isPersistent = $isPersistent;
    }
}