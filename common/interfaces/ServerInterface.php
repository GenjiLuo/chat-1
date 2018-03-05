<?php

namespace common\interfaces;
/**
 * Interface ServerInterface
 * @package common\interfaces
 */
interface ServerInterface
{
    /**
     * @return mixed
     */
    public function run();

    public function get();
}
