<?php
namespace common\lib\exception;

use Throwable;

class ForbiddenException extends \Exception{
    public function __construct(string $message = "", int $code = 403, Throwable $previous = null)
    {
        $message = "请求不被允许";
        parent::__construct($message, $code, $previous);
    }
}