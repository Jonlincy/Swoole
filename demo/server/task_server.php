<?php
/**
 * Created by PhpStorm.
 * User: Jonlinc
 * Motto : Missed is missed.
 * Date: 2019/8/26 0026
 * Time: 下午 20:17
 */
$serv = new Swoole\Server("127.0.0.1", 9501, SWOOLE_BASE);

$serv->set(array(
    'worker_num' => 2,
    'task_worker_num' => 4,
));

$serv->on('Receive', function(Swoole\Server $serv, $fd, $from_id, $data) {
    echo "接收数据" . $data . "\n";
    $data = trim($data);
    $task_id = $serv->task($data, 0);
    $serv->send($fd, "分发任务，任务id为$task_id\n");
});

$serv->on('Task', function (Swoole\Server $serv, $task_id, $from_id, $data) {
    echo "Tasker进程接收到数据";
    echo "#{$serv->worker_id}\tonTask: [PID={$serv->worker_pid}]: task_id=$task_id, data_len=".strlen($data).".".PHP_EOL;
    $serv->finish($data);
});

$serv->on('Finish', function (Swoole\Server $serv, $task_id, $data) {
    echo "Task#$task_id finished, data_len=".strlen($data).PHP_EOL;
});

$serv->on('workerStart', function($serv, $worker_id) {
    global $argv;
    if($worker_id >= $serv->setting['worker_num']) {
        swoole_set_process_name("php {$argv[0]}: task_worker");
    } else {
        swoole_set_process_name("php {$argv[0]}: worker");
    }
});

$serv->start();