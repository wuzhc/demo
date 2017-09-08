<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年04月07日
 * Time: 16:10
 */

?>

<script src="https://cdn.bootcss.com/socket.io/1.7.3/socket.io.min.js"></script>
<!--<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>-->
<script>

    if ('WebSocket' in window) {
        // 创建websocket实例
        var socket = io.connect('ws://localhost:8080');

        // 建立socket连接触发事件
        socket.onopen = function(event) {
            // 使用连接发送数据
            socket.send('hello websocket');
        };

        // 监听服务器返回的数据
        socket.onmessage = function(event) {
            console.log("client has received a message", event);
        };

        // 关闭时候触发
        socket.onclose = function(event) {
            console.log("close websocket");
        };

        // 错误时触发
        socket.onerror = function(event) {
            console.log("this is an error");
        };


    } else {
        alert('can not support websocket');
    }

</script>
