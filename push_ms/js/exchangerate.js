var ws = {};
var client_id = 0;
var config = {
    'server' : 'ws://192.168.1.131:9504',
    'flash_websocket' : true
}
function showOverlay(){
    var opts = {
        lines: 11 // The number of lines to draw
        , length: 23 // The length of each line
        , width: 13 // The line thickness
        , radius: 33 // The radius of the inner circle
        , scale: 0.75 // Scales overall size of the spinner
        , corners: 1 // Corner roundness (0..1)
        , color: 'white' // #rgb or #rrggbb or array of colors
        , opacity: 0.1 // Opacity of the lines
        , rotate: 8 // The rotation offset
        , direction: 1 // 1: clockwise, -1: counterclockwise
        , speed: 0.5 // Rounds per second
        , trail: 90 // Afterglow percentage
        , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
        , zIndex: 2e9 // The z-index (defaults to 2000000000)
        , className: 'spinner' // The CSS class to assign to the spinner
        , top: '28%' // Top position relative to parent
        , left: '50%' // Left position relative to parent
        , shadow: false // Whether to render a shadow
        , hwaccel: false // Whether to use hardware acceleration
        , position: 'absolute' // Element positioning
    };
    var spinner = new Spinner(opts).spin();
    $("#loading").html(spinner.el);
    $(".overlay").show();
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
    showOverlay();
    listenEvent();
    $('#container').highcharts({
        chart: {
            type: 'spline',
            animation: Highcharts.svg, // don't animate in old IE
            marginRight: 10
        },
        title: {
            text: '在线实时汇率'
        },
        xAxis:{
            type: 'datetime',
            tickPixelInterval: 150
        },
        
        yAxis: {
            tickInterval:1,
            title: {
                enabled: true,
                text: 'CNY : USD'
            } ,
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
                    Highcharts.numberFormat(this.y, 2);
            }
        },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
        series: [{
            data: []
        }]
    });
});

function listenEvent() {
    /**
     * 连接建立时触发
     */
    ws.onopen = function (e) {
        var msg = {'cmd':'connection'};
        ws.send(msg);
    };

    ws.onmessage = function (e) {
        var message = eval('('+ e.data +')');
        var cmd = message.data.cmd;
        if (cmd == 'connection')
        {
            if(message.data.status == 1) 
            {
                $(".pubArea").show();
                hideOverlay();                
            }
        }
        else if (cmd == 'gethistory')
        {
            $(".pubArea").show();
            hideOverlay();

            alert( "收到消息了:"+e.data );
        }
        else if (cmd == 'push')
        {
            var data = message.data.rate;
            var x = parseFloat(data.date);
            var y = parseFloat(data.w2rmb);
            var chart = $('#container').highcharts();
            chart.series.addPoint([x,y], true, true);
        }
    };

    /**
     * 连接关闭事件
     */
    ws.onclose = function (e) {
        $(".pubArea").hide();
        showOverlay();
    };

    /**
     * 异常事件
     */
    ws.onerror = function (e) {
        $(".pubArea").hide();
        showOverlay();
    };
}