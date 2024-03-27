<?php

//创建WebSocket Server对象，监听0.0.0.0:8080端口
$ws = new Swoole\WebSocket\Server('0.0.0.0', 5860, SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);

$ws->set([
    'daemonize' => true, //守护进程化。
    'worker_num' => 4,
    //配置SSL证书和密钥路径
    'ssl_cert_file' => '/www/server/panel/vhost/cert/whaleexc.com/fullchain.pem',
    'ssl_key_file' => '/www/server/panel/vhost/cert/whaleexc.com/privkey.pem'
]);

//监听WebSocket连接打开事件
$ws->on('Open', function ($ws, $request) {
    echo "request fd: {$request->fd}\n";
});

//监听WebSocket消息事件
$ws->on('Message', function ($ws, $frame) {
});

//监听WebSocket连接关闭事件
$ws->on('Close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$tcp = $ws->listen("0.0.0.0", 2070, SWOOLE_SOCK_TCP);
$tcp->set([
    'open_length_check'     => true,        // 开启协议解析
    'package_length_type'   => 'N',         // 长度字段的类型
    'package_length_offset' => 0,           // 第几个字节是包长度的值
    'package_body_offset'   => 4,           // 第几个字节开始计算长度
    'package_max_length'    => 81920        // 数据包最大长度
]);
$tcp->on('receive', function ($server, $fd, $reactor_id, $data) {
    $info = unpack('N', $data);
    $len = $info[1];
    $data = substr($data, -$len);

    //仅遍历websocket端口的连接，不是$tcp
    $websocket = $server->ports[0];
    foreach ($websocket->connections as $_fd) {
        if ($server->exist($_fd)) {
            $server->push($_fd, $data);
        }
    }
    //$server->send($fd, 'receive: '.$data);
});

$ws->start();
