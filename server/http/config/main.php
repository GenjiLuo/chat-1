<?php
return [
    "router" => [
        "class" => "server\http\Router",
    ],
    "log" => [
        "class" => "common\lib\Log",
        'filePath'=> BASE_ROOT."/runtime/http"
    ]
];