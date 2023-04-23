<?php

namespace App\Constant;

use App\TelegramCommand\BugTrack;
use App\TelegramCommand\Cancel;
use App\TelegramCommand\Help;
use App\TelegramCommand\MySettings;
use App\TelegramCommand\RemoveModel;
use App\TelegramCommand\RemoveToken;
use App\TelegramCommand\SetModel;
use App\TelegramCommand\SetToken;
use App\TelegramCommand\Start;

class TelegramCommandRegistry
{
    public static function getShowCommands(): array
    {
        return [
            Help::class,
            MySettings::class,
            Cancel::class,
            SetToken::class,
            RemoveToken::class,
            SetModel::class,
            RemoveModel::class,
            BugTrack::class,
        ];
    }

    public static function getListenCommands(): array
    {
        return [
            Start::class,
            Help::class,
            MySettings::class,
            Cancel::class,
            SetToken::class,
            RemoveToken::class,
            SetModel::class,
            RemoveModel::class,
            BugTrack::class,
        ];
    }
}