var ws = {};
// client_id 作为 user id 测试
var client_id = Date.parse(new Date());
var config = {
    'server' : 'ws://104.194.84.229:9502',
    'flash_websocket' : true
}
function hideOverlay(){
    $(".overlay").hide();
    $("#loading").html('');
}
$(document).ready(function () {
    //使用原生WebSocket
    if (window.WebSocket || window.MozWebSocket)
    {
        ws = new WebSocket(config.server);
    }
    //使用flash websocket
    else if (config.flash_websocket)
    {
        WEB_SOCKET_SWF_LOCATION = "./flash-websocket/WebSocketMain.swf";
        $.getScript("./flash-websocket/swfobject.js", function () {
            $.getScript("./flash-websocket/web_socket.js", function () {
                ws = new WebSocket(config.server);
            });
        });
    }
    //使用http xhr长轮循
    else
    {
        ws = new Comet(config.server);
    }
    listenEvent();
});

function listenEvent() {
    /**
     * 连接建立时触发
     */
    ws.onopen = function (e) {
        var msg = {"method":"join","uid":client_id,"hobby":1};
        ws.send(JSON.stringify(msg));          
    };

    ws.onmessage = function (e) {
        var message = eval('('+ e.data +')');
        var cmd = message.data.method;
        if (cmd == 'connection')
        {
            if(message.data.status == 1) 
            {
               //alert('connection ok');          
            }
        }
        else if (cmd == 'push')
        {
            var server_data = message.data;
            $("#container").append(server_data.data + "<br/>");
        }
    };

    /**
     * 连接关闭事件
     */
    ws.onclose = function (e) {
        $(".pubArea").hide();
    };

    /**
     * 异常事件
     */
    ws.onerror = function (e) {
        $(".pubArea").hide();
    };
}