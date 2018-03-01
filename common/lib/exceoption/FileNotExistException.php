<?php
namespace common\lib\exception;

use Throwable;

class FileNotExistException extends \Exception{

    public function __construct(string $file = "", int $code = 500, Throwable $previous = null)
    {
        $message  = $file." not exist";
        parent::__construct($message, $code, $previous);
    }
}