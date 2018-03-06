<?php
return [
    "router"=>[
        "class" => "server\ws\Router",
    ],
    "log" => [
        "class" => "common\lib\Log",
        'filePath'=> BASE_ROOT."/runtime/ws"
    ]
];