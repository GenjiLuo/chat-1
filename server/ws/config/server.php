<?php

return [
    'reactor_num' => 4,
    'worker_num' => 4,
    'max_request'=> 0,
    //max_conn (max_connection)
    'task_worker_num' => 4
//    task_ipc_mode => 1, //使用unix socket通信，默认模式
    //task_ipc_mode => 2, //使用消息队列通信
    //task_ipc_mode => 3, //使用消息队列通信，并设置为争抢模式
    //task_max_request => 0,  //设置task进程的最大任务数
    //task_tmpdir  //设置task的数据临时目录
    //dispatch_mode => 1, //数据包分发策略
    //daemonize => 0  //守护进程化
    //log_file   //指定swoole错误日志文件
    //log_level  //设置swoole_server错误日志打印的等级，范围是0-5
];