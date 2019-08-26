<?php
/**
 * Created by PhpStorm.
 * User: Jonlinc
 * Motto : Missed is missed.
 * Date: 2019/8/26 0026
 * Time: 下午 16:09
 */
$server = new swoole_websocket_server('0.0.0.0',8812);

//方式一
//$server->on('open',function (swoole_websocket_server $server,$request){
//    echo "server: handshake success with fd{$request->fd}\n";
//});

//方式二
//监听webSocket连接打开事件
$server->on('open','onOpen');
function onOpen($server,$request){
    print_r($request->fd);
}

//监听webSocket消息事件
$server->on('message',function (swoole_websocket_server $server,$frame){
    echo "receive from {$frame->fd}:{$frame->data},opCode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd,"push is success!");
});

$server->on('close',function ($ser,$fd){
    echo "Client $fd is closed.\n";
});

$server->start();