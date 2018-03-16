<?php
namespace common\lib\exception;

use Throwable;

class ForbiddenException extends \Exception{
    public function __construct(string $message = "", int $code = 403, Throwable $previous = null)
    {
        $message = "request not forbidden";
        parent::__construct($message, $code, $previous);
    }
}