<?php

namespace App\Constant;

enum TelegramSystemMessageText: string
{
    case COMMAND_POST_PROCESS_ERROR = "Something went wrong";
    case MESSAGE_TYPE_ERROR = "Seriously? \nI will not accept this message :)";
    case ALREADY_PROCESSING = "I'm already processing your message";
    case WAIT_PROCESS = "I'm diving into the depths of my algorithms...";
    case QUEUE_ERROR = "I was unable to process your message. \nPlease send again, this time I will definitely succeed";
}