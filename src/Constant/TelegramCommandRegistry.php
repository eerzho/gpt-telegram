<?php

namespace App\Constant;

use App\TelegramCommand\BugTrack;
use App\TelegramCommand\Cancel;
use App\TelegramCommand\Help;
use App\TelegramCommand\Settings;
use App\TelegramCommand\Start;

class TelegramCommandRegistry
{
    public static function getShowCommands(): array
    {
        return [
            Help::class,
            Settings::class,
            Cancel::class,
//            SetToken::class,
//            RemoveToken::class,
//            SetModel::class,
//            RemoveModel::class,
            BugTrack::class,
        ];
    }

    public static function getListenCommands(): array
    {
        return [
            Start::class,
            Help::class,
            Settings::class,
            Cancel::class,
//            SetToken::class,
//            RemoveToken::class,
//            SetModel::class,
//            RemoveModel::class,
            BugTrack::class,
        ];
    }

    public static function getForKeyboard(): array
    {
        return [
            BugTrack::class,
            Settings::class,
            Cancel::class,
        ];
    }
}