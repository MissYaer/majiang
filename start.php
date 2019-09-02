<?php
/**
 * Created by PhpStorm.
 * User: MissYang
 * Date: 2019/8/7
 * Time: 11:15
 *　   　 ／＞　　フ
 *　　  　|  　~　 ~ l
 *　   　／` ミ＿xノ
 *　　 /　 ヽ　　 ﾉ
 *／￣|　　 |　|　|
 *| (￣ヽ＿_ヽ_)__)
 *＼二つ
 */

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/func.php';

$ws = new swoole_websocket_server("0.0.0.0", 9501);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    $ws->push($request->fd, json_encode(['msg' => 'welocome','type' => 'welocome']));
});

/**
 * 监听WebSocket消息事件
 */
$ws->on('message', function ($ws, $frame) {
    $data = json_decode($frame->data,true);
    switch ($data['type']){
        // 开始游戏
        case 'start_game':
            $majingService = new \Service\Majiang();
            $data = $majingService->startGame($data['random_cookie']);
            $ws->push($frame->fd, $data);
            break;
        // 初始化 打牌
        case 'play_car':
            $playService = new \Service\Play($data['data']);
            $data = $playService->play($data['data'], $data['random_cookie'], $data['people']);
            $ws->push($frame->fd, $data);
            break;
        // 打牌后 计算三家是否可碰 可胡
        case 'three_families':
            $playService = new \Service\Play($data['brand']);
            $data = $playService->three_families($data['brand'], $data['random_cookie'],$data['people']);
            $ws->push($frame->fd, $data);
            break;
        default:
            break;
    }
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();