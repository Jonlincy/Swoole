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
        $this->ws = new swoole_websocket_server(self::HOST,self::PORT);
        $this->ws->on("open",[$this,"onOpen"]);
        $this->ws->on("message",[$this,"onMessage"]);
        $this->ws->on("task",[$this,"onTask"]);
        $this->ws->on("finish",[$this,"onFinish"]);
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
        //任务测试，假设有10秒等待时间
        $data = [
            'task' => 1,
            'fd' => $frame->fd
        ];

        $ws->task($data);
        $ws->push($frame->fd,"Server push:" . date('Y-m-d H:i:s'));
    }

    /**
     * @param $server
     * @param $taskId
     * @param $workId
     * @param $data
     * @author hjl
     * @Date: 2019/8/26 0026
     * @return string
     */
    public function onTask($server,$taskId,$workId,$data)
    {
        print_r($data);
        //耗时场景10s
        sleep(10);
        return "on task finish"; // 告诉Worker进程(返回给OnFinish)

    }

    /**
     * @param $server
     * @param $taskId
     * @param $data
     * @author hjl
     * @Date: 2019/8/26 0026
     */
    public function onFinish($server,$taskId,$data)
    {
        echo "taskId is:{$taskId}\n";
        echo "finish success data is:{$data}";
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