<?php
return [
    "message"=>\server\ws\action\Message::class,
    "open"=>\server\ws\action\Open::class,
    "request"=>"",
    "close"=>\server\ws\action\Close::class,
    'task'=>\server\ws\action\Task::class,
    'channel'=>\server\ws\action\Channel::class
];