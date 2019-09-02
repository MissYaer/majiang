var random_cookie;

/**
 *  开始游戏 点击事件
 */
$("#start_game").click(function () {
    // delete div
    $(".majiang").remove();
    random_cookie = rand();
    a = new Object();
    a.type = 'start_game';
    a.data = null;
    sendMsg(a)
});

function rand() {
    return 'test_room';
}