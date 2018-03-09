<?php

namespace common\model;

use common\lib\DB;

class UserFriendModel extends DB
{
    public static $tableName = "user_to_friend";
}