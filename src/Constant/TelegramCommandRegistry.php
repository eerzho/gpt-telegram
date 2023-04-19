<?php

namespace App\Constant;

use App\TelegramCommand\Cancel;
use App\TelegramCommand\Help;
use App\TelegramCommand\MySettings;
use App\TelegramCommand\RemoveMaxTokens;
use App\TelegramCommand\RemoveModel;
use App\TelegramCommand\RemoveTemperature;
use App\TelegramCommand\RemoveToken;
use App\TelegramCommand\SetMaxTokens;
use App\TelegramCommand\SetModel;
use App\TelegramCommand\SetTemperature;
use App\TelegramCommand\SetToken;
use App\TelegramCommand\Start;

class TelegramCommandRegistry
{
    public static function getActiveCommands(): array
    {
        return [
            Start::class,
            Help::class,
            MySettings::class,
            SetToken::class,
            RemoveToken::class,
            SetModel::class,
            RemoveModel::class,
            SetTemperature::class,
            RemoveTemperature::class,
            SetMaxTokens::class,
            RemoveMaxTokens::class,
            Cancel::class,
        ];
    }
}