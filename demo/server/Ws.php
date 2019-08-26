<?php
/**
 * Created by PhpStorm.
 * User: Jonlinc
 * Motto : Missed is missed.
 * Date: 2019/8/26 0026
 * Time: 下午 17:41
 * WebSocket 基类封装
 */

class Ws
{
    const HOST = '0.0.0.0';
    const PORT = 8812;

    public $ws = null;

    public function __construct()
    {
        $this->ws = swoole_websocket_server(self::HOST,self::PORT);
        $this->ws->on("open",[$this,"onOpen"]);
        $this->ws->on("message",[$this,"onMessage"]);
        $this->ws->on("close",[$this,"onClose"]);

        $this->ws->start();
    }

    /**
     * 监听WS连接事件
     * @param $ws
     * @param $request
     * @author hjl
     * @Date: 2019/8/26 0026
     */
    public function onOpen($ws,$request)
    {
        var_dump($request->fd);
    }

    /**
     * 监听Ws消息事件
     * @param $ws
     * @param $frame
     * @author hjl
     * @Date: 2019/8/26 0026
     */
    public function onMessage($ws,$frame)
    {
        echo "Server push message:{$frame->data}\n";
        $ws->push($frame->fd,"Server push:" . date('Y-m-d H:i:s'));
    }

    /**
     * 关闭连接
     * @param $ws
     * @param $fd
     * @author hjl
     * @Date: 2019/8/26 0026
     */
    public function onClose($ws,$fd)
    {
        echo "Client:{$fd} closed\n";
    }

}

$obj = new Ws();